<?php

namespace App\Services;

use Carbon\Carbon;
use Google\Client;
use Google\Service\Drive;
use RuntimeException;

class DriveEvidenService
{
    private const MIME_FOLDER = 'application/vnd.google-apps.folder';

    private Drive $drive;

    /** @var array<string, array{folders: array, files: array}> */
    private array $memo = [];

    public function __construct()
    {
        $path = config('eviden.credentials');
        if (!is_file($path)) {
            throw new RuntimeException("File service account tidak ditemukan: {$path}");
        }

        $client = new Client();
        $client->setAuthConfig($path);
        $client->setScopes([Drive::DRIVE_READONLY]);
        $this->drive = new Drive($client);
    }

    /**
     * Setara ambilDataDashboard() di Apps Script.
     * Struktur: Kategori -> Sub Kategori -> Indikator.
     */
    public function ambilData(): array
    {
        $folderSem2 = config('eviden.folder_sem2');
        if (!$folderSem2) {
            throw new RuntimeException('EVIDEN_FOLDER_SEM2 belum diisi di file .env');
        }

        $petaSem1 = $this->petaLinkSem1();
        $hasil = [];

        foreach ($this->subFolders($folderSem2) as $fUtama) {
            $namaUtama = trim($fUtama->getName());

            foreach ($this->subFolders($fUtama->getId()) as $fSub) {
                $namaSub = trim($fSub->getName());

                foreach ($this->subFolders($fSub->getId(), true) as $fInd) {
                    $a = $this->analisis($fInd->getId(), 'Utama', $fInd->getName());
                    $kunci = $namaUtama . '|' . $namaSub . '|' . trim($fInd->getName());

                    $hasil[] = [
                        'id'            => $fInd->getId(),
                        'kategori'      => $namaUtama,
                        'subKategori'   => $namaSub,
                        'indikator'     => $fInd->getName(),
                        'link'          => $this->urlFolder($fInd->getId()),   // Semester II
                        'linkSem1'      => $petaSem1[$kunci] ?? '',            // Semester I
                        'jumlah'        => $a['total'],
                        'rincian'       => $a['rincian'],
                        'update'        => $a['terakhir']
                            ? $a['terakhir']->copy()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i')
                            : '-',
                        'status'        => $a['total'] > 0 ? 'Terisi' : 'Kosong',
                        'terverifikasi' => false, // diisi controller dari database
                    ];
                }
            }
        }

        return $hasil;
    }

    /** Peta "kategori|sub|indikator" => url folder Semester I. */
    private function petaLinkSem1(): array
    {
        $folderSem1 = config('eviden.folder_sem1');
        if (!$folderSem1) {
            return [];
        }

        $peta = [];
        foreach ($this->subFolders($folderSem1) as $fU) {
            foreach ($this->subFolders($fU->getId()) as $fS) {
                foreach ($this->subFolders($fS->getId()) as $fI) {
                    $kunci = trim($fU->getName()) . '|' . trim($fS->getName()) . '|' . trim($fI->getName());
                    $peta[$kunci] = $this->urlFolder($fI->getId());
                }
            }
        }

        return $peta;
    }

    /** Setara analisisFolderMendalam() di Apps Script (rekursif). */
    private function analisis(string $folderId, string $status, string $namaFolder): array
    {
        $isi = $this->isiFolder($folderId);
        $total = count($isi['files']);
        $terakhir = null;
        $rincian = [];

        foreach ($isi['files'] as $f) {
            $t = Carbon::parse($f->getModifiedTime());
            if (!$terakhir || $t->gt($terakhir)) {
                $terakhir = $t;
            }
        }

        if ($total > 0) {
            $label = $status === 'Utama' ? 'Halaman Depan Indikator' : $namaFolder;
            $rincian[] = "{$label} ({$total} file)";
        }

        foreach ($isi['folders'] as $sub) {
            $h = $this->analisis($sub->getId(), 'Sub', $sub->getName());
            $total += $h['total'];
            if ($h['terakhir'] && (!$terakhir || $h['terakhir']->gt($terakhir))) {
                $terakhir = $h['terakhir'];
            }
            $rincian = array_merge($rincian, $h['rincian']);
        }

        return ['total' => $total, 'terakhir' => $terakhir, 'rincian' => $rincian];
    }

    /** Daftar sub folder, opsional diurutkan menurut angka di nama. */
    private function subFolders(string $folderId, bool $urutNumerik = false): array
    {
        $folders = $this->isiFolder($folderId)['folders'];

        if ($urutNumerik) {
            usort($folders, fn ($a, $b) =>
                (int) preg_replace('/\D/', '', $a->getName()) <=> (int) preg_replace('/\D/', '', $b->getName())
            );
        }

        return $folders;
    }

    /** Satu panggilan API (berhalaman) per folder, di-memo selama request. */
    private function isiFolder(string $folderId): array
    {
        if (isset($this->memo[$folderId])) {
            return $this->memo[$folderId];
        }

        $folders = [];
        $files = [];
        $pageToken = null;

        do {
            $params = [
                'q'                         => "'{$folderId}' in parents and trashed = false",
                'fields'                    => 'nextPageToken, files(id, name, mimeType, modifiedTime)',
                'pageSize'                  => 1000,
                'supportsAllDrives'         => true,
                'includeItemsFromAllDrives' => true,
            ];
            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }

            $res = $this->drive->files->listFiles($params);

            foreach ($res->getFiles() as $f) {
                if ($f->getMimeType() === self::MIME_FOLDER) {
                    $folders[] = $f;
                } else {
                    $files[] = $f;
                }
            }

            $pageToken = $res->getNextPageToken();
        } while ($pageToken);

        return $this->memo[$folderId] = ['folders' => $folders, 'files' => $files];
    }

    private function urlFolder(string $id): string
    {
        return "https://drive.google.com/drive/folders/{$id}";
    }
}
