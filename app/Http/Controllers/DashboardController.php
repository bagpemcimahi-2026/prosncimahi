<?php

namespace App\Http\Controllers;

use App\Models\Verifikasi;
use App\Services\DriveEvidenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class DashboardController extends Controller
{
    private const CACHE_KEY = 'eviden.data';

    public function index()
    {
        return view('dashboard');
    }

    public function data(Request $request): JsonResponse
    {
        set_time_limit(300); // scan Drive bisa lama

        try {
            if ($request->boolean('refresh')) {
                Cache::forget(self::CACHE_KEY);
            }

            $data = Cache::remember(
                self::CACHE_KEY,
                config('eviden.cache_ttl'),
                fn () => app(DriveEvidenService::class)->ambilData()
            );

            // Status verifikasi dibaca langsung dari database (tidak ikut cache)
            $verif = Verifikasi::where('terverifikasi', true)->pluck('folder_id')->flip();

            foreach ($data as &$row) {
                $row['terverifikasi'] = $verif->has($row['id']);
            }
            unset($row);

            return response()->json($data);
        } catch (Throwable $e) {
            Log::error('Gagal memuat data eviden: ' . $e->getMessage());

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function toggle(string $folderId): JsonResponse
    {
        if (!preg_match('/^[A-Za-z0-9_-]{10,}$/', $folderId)) {
            return response()->json(['message' => 'ID folder tidak valid'], 422);
        }

        $v = Verifikasi::firstOrNew(['folder_id' => $folderId]);
        $v->terverifikasi = !$v->terverifikasi;
        $v->save();

        return response()->json(['terverifikasi' => $v->terverifikasi]);
    }
}
