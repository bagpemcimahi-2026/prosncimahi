<!DOCTYPE html>
<html lang="id">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Monitoring Eviden KDH 2026</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #0b0f1a;
      --bg2: #111827;
      --bg3: #1a2236;
      --border: rgba(255,255,255,0.07);
      --border2: rgba(255,255,255,0.13);
      --accent: #3b82f6;
      --accent2: #60a5fa;
      --green: #10b981;
      --green-bg: rgba(16,185,129,0.1);
      --green-border: rgba(16,185,129,0.25);
      --red: #f43f5e;
      --red-bg: rgba(244,63,94,0.1);
      --red-border: rgba(244,63,94,0.25);
      --amber: #f59e0b;
      --text: #f1f5f9;
      --text2: #94a3b8;
      --text3: #475569;
      --radius: 12px;
      --radius-sm: 7px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image:
        linear-gradient(rgba(59,130,246,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(59,130,246,0.03) 1px, transparent 1px);
      background-size: 40px 40px;
      pointer-events: none;
      z-index: 0;
    }

    .wrapper { position: relative; z-index: 1; max-width: 1700px; margin: 0 auto; padding: 28px 32px 48px; }

    /* HEADER */
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; padding-bottom: 24px; border-bottom: 1px solid var(--border); }
    .header-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: var(--accent2); margin-bottom: 6px; display: flex; align-items: center; gap: 7px; }
    .header-eyebrow::before { content: ''; display: inline-block; width: 20px; height: 2px; background: var(--accent); border-radius: 2px; }
    .header-title { font-size: 26px; font-weight: 800; color: var(--text); line-height: 1.2; letter-spacing: -0.5px; }
    .header-title span { color: var(--accent2); }
    .header-subtitle { font-size: 13px; color: var(--text2); margin-top: 6px; }
    .header-right { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }

    .btn-refresh { display: flex; align-items: center; gap: 8px; background: var(--accent); color: white; border: none; padding: 10px 20px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600; font-family: inherit; cursor: pointer; transition: all 0.2s; }
    .btn-refresh:hover { background: var(--accent2); transform: translateY(-1px); box-shadow: 0 4px 20px rgba(59,130,246,0.4); }
    .btn-refresh svg { width: 15px; height: 15px; transition: transform 0.4s; }
    .btn-refresh.loading svg { animation: spin 0.8s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .last-updated { font-size: 11px; color: var(--text3); font-family: 'JetBrains Mono', monospace; }

    /* STATS */
    .stats-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; margin-bottom: 24px; }
    .stat-card { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px 20px; position: relative; overflow: hidden; transition: border-color 0.2s; }
    .stat-card:hover { border-color: var(--border2); }
    .stat-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; border-radius: var(--radius) var(--radius) 0 0; }
    .stat-card.total::after { background: var(--accent); }
    .stat-card.terisi::after { background: var(--green); }
    .stat-card.kosong::after { background: var(--red); }
    .stat-card.pct::after { background: var(--amber); }
    .stat-card.verif::after { background: #a855f7; }
    .stat-label { font-size: 11px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text3); margin-bottom: 8px; }
    .stat-value { font-size: 32px; font-weight: 800; line-height: 1; letter-spacing: -1px; font-family: 'JetBrains Mono', monospace; }
    .stat-card.total .stat-value { color: var(--accent2); }
    .stat-card.terisi .stat-value { color: var(--green); }
    .stat-card.kosong .stat-value { color: var(--red); }
    .stat-card.pct .stat-value { color: var(--amber); }
    .stat-card.verif .stat-value { color: #c084fc; }
    .stat-sub { font-size: 11px; color: var(--text3); margin-top: 5px; }
    .stat-progress { margin-top: 10px; height: 3px; background: var(--border); border-radius: 3px; overflow: hidden; }
    .stat-progress-fill { height: 100%; border-radius: 3px; transition: width 0.8s cubic-bezier(0.4,0,0.2,1); }
    .stat-card.pct .stat-progress-fill { background: var(--amber); }
    .stat-card.verif .stat-progress-fill { background: #a855f7; }

    /* FILTER */
    .filter-bar { display: flex; gap: 10px; margin-bottom: 18px; flex-wrap: wrap; align-items: center; }
    .filter-label { font-size: 12px; color: var(--text3); font-weight: 500; margin-right: 4px; }
    .filter-btn { padding: 6px 14px; border-radius: 99px; border: 1px solid var(--border2); background: transparent; color: var(--text2); font-size: 12px; font-family: inherit; font-weight: 500; cursor: pointer; transition: all 0.15s; }
    .filter-btn:hover { border-color: var(--accent); color: var(--accent2); }
    .filter-btn.active { background: var(--accent); border-color: var(--accent); color: white; }
    .search-box { margin-left: auto; position: relative; }
    .search-box input { background: var(--bg2); border: 1px solid var(--border2); border-radius: var(--radius-sm); color: var(--text); font-family: inherit; font-size: 13px; padding: 7px 12px 7px 34px; outline: none; width: 220px; transition: border-color 0.2s; }
    .search-box input:focus { border-color: var(--accent); }
    .search-box input::placeholder { color: var(--text3); }
    .search-box svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text3); width: 15px; height: 15px; }

    /* TABLE */
    .table-wrap { background: var(--bg2); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead tr { background: var(--bg3); border-bottom: 1px solid var(--border2); }
    th { padding: 12px 14px; text-align: left; font-size: 11px; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; color: var(--text3); white-space: nowrap; }
    th.center { text-align: center; }
    tbody tr { border-bottom: 1px solid var(--border); transition: background 0.15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: rgba(59,130,246,0.04); }
    td { padding: 12px 14px; vertical-align: middle; }
    td.center { text-align: center; }
    td.top { vertical-align: top; }

    .tag-kategori { display: inline-flex; align-items: center; background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.2); color: var(--accent2); font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 99px; white-space: nowrap; max-width: 170px; overflow: hidden; text-overflow: ellipsis; }
    .tag-sub { font-size: 12px; color: var(--text2); font-weight: 500; line-height: 1.4; }
    .tag-indikator { display: inline-block; background: var(--bg3); border: 1px solid var(--border2); color: var(--text); font-size: 11px; font-weight: 600; font-family: 'JetBrains Mono', monospace; padding: 3px 9px; border-radius: var(--radius-sm); }

    .status-pill { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 99px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .status-pill.terisi { background: var(--green-bg); border: 1px solid var(--green-border); color: var(--green); }
    .status-pill.kosong { background: var(--red-bg); border: 1px solid var(--red-border); color: var(--red); }
    .status-dot { width: 6px; height: 6px; border-radius: 50%; }
    .terisi .status-dot { background: var(--green); box-shadow: 0 0 6px var(--green); }
    .kosong .status-dot { background: var(--red); box-shadow: 0 0 6px var(--red); }

    .file-count { font-family: 'JetBrains Mono', monospace; font-size: 15px; font-weight: 700; color: var(--accent2); }
    .file-count.zero { color: var(--text3); }

    .rincian-list { display: flex; flex-direction: column; gap: 3px; }
    .rincian-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--text2); background: var(--bg3); padding: 3px 8px; border-radius: 5px; border-left: 2px solid var(--accent); line-height: 1.4; }
    .rincian-item svg { flex-shrink: 0; color: var(--accent2); width: 11px; height: 11px; }
    .rincian-empty { font-size: 11px; color: var(--text3); font-style: italic; }

    .update-time { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--text3); }

    .btn-drive { display: inline-flex; align-items: center; gap: 5px; padding: 5px 11px; border-radius: var(--radius-sm); border: 1px solid var(--border2); background: transparent; color: var(--text2); font-size: 11px; font-weight: 600; font-family: inherit; text-decoration: none; transition: all 0.15s; white-space: nowrap; }
    .btn-drive:hover { border-color: var(--accent); color: var(--accent2); background: rgba(59,130,246,0.08); }
    .btn-drive svg { width: 12px; height: 12px; }
    .aksi-group { display: inline-flex; gap: 6px; }
    .btn-drive.sem1 { border-color: rgba(148,163,184,0.45); }
    .btn-drive.sem2 { border-color: var(--accent); color: var(--accent2); }
    .btn-drive.disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }

    /* ── TOMBOL VERIFIKASI ── */
    .btn-verif {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 13px;
      border-radius: var(--radius-sm);
      border: none;
      font-size: 12px;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      transition: all 0.22s cubic-bezier(0.4,0,0.2,1);
      white-space: nowrap;
      position: relative;
      overflow: hidden;
      letter-spacing: 0.02em;
    }

    .btn-verif::before {
      content: '';
      position: absolute;
      inset: 0;
      opacity: 0;
      transition: opacity 0.2s;
      background: white;
    }

    .btn-verif:active::before { opacity: 0.08; }

    /* Belum diverifikasi = MERAH */
    .btn-verif.belum {
      background: var(--red-bg);
      border: 1px solid var(--red-border);
      color: var(--red);
    }
    .btn-verif.belum:hover {
      background: rgba(244,63,94,0.18);
      border-color: var(--red);
      box-shadow: 0 0 12px rgba(244,63,94,0.25);
      transform: translateY(-1px);
    }

    /* Sudah diverifikasi = HIJAU */
    .btn-verif.sudah {
      background: var(--green-bg);
      border: 1px solid var(--green-border);
      color: var(--green);
    }
    .btn-verif.sudah:hover {
      background: rgba(16,185,129,0.18);
      border-color: var(--green);
      box-shadow: 0 0 12px rgba(16,185,129,0.25);
      transform: translateY(-1px);
    }

    /* Loading state tombol */
    .btn-verif.loading-verif {
      opacity: 0.6;
      cursor: wait;
      pointer-events: none;
    }

    .btn-verif svg { width: 13px; height: 13px; flex-shrink: 0; }

    /* Pulse animation saat baru berubah */
    @keyframes pulse-green {
      0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.5); }
      70%  { box-shadow: 0 0 0 8px rgba(16,185,129,0); }
      100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
    }
    @keyframes pulse-red {
      0%   { box-shadow: 0 0 0 0 rgba(244,63,94,0.5); }
      70%  { box-shadow: 0 0 0 8px rgba(244,63,94,0); }
      100% { box-shadow: 0 0 0 0 rgba(244,63,94,0); }
    }
    .btn-verif.pulse-g { animation: pulse-green 0.5s ease; }
    .btn-verif.pulse-r { animation: pulse-red 0.5s ease; }

    /* GROUP HEADER */
    .group-header td { background: var(--bg3); padding: 8px 14px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--accent2); border-bottom: 1px solid var(--border2); }

    /* LOADING */
    .loading-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 32px; gap: 16px; }
    .loader-ring { width: 44px; height: 44px; border: 3px solid var(--border2); border-top-color: var(--accent); border-radius: 50%; animation: spin 0.8s linear infinite; }
    .loading-text { font-size: 14px; color: var(--text2); font-weight: 500; }
    .loading-sub { font-size: 12px; color: var(--text3); font-family: 'JetBrains Mono', monospace; }

    .empty-state { display: flex; flex-direction: column; align-items: center; padding: 60px; gap: 12px; color: var(--text3); }
    .empty-state svg { width: 40px; height: 40px; opacity: 0.3; }
    .empty-state p { font-size: 14px; }

    /* FOOTER */
    .footer { margin-top: 28px; padding-top: 18px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .footer-text { font-size: 11px; color: var(--text3); }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
    tbody tr { animation: fadeInUp 0.2s ease both; }

    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: var(--bg); }
    ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 6px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--text3); }
  </style>
</head>
<body>
<div class="wrapper">

  <!-- HEADER -->
  <div class="header">
    <div class="header-left">
      <div class="header-eyebrow">Sistem Pemantauan Eviden</div>
      <div class="header-title">Monitoring Eviden <span>KDH 2026</span></div>
      <div class="header-subtitle">Dashboard Real-Time · Multi-Level Sub Folder Indikator · Semester I &amp; II</div>
    </div>
    <div class="header-right">
      <button class="btn-refresh" onclick="muatDataDashboard(true)" id="btnRefresh">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
          <path d="M21 3v5h-5"/>
          <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/>
          <path d="M3 21v-5h5"/>
        </svg>
        Refresh Data
      </button>
      <div class="last-updated" id="lastUpdated">Belum dimuat</div>
    </div>
  </div>

  <!-- STAT CARDS -->
  <div class="stats-row">
    <div class="stat-card total">
      <div class="stat-label">Total Indikator</div>
      <div class="stat-value" id="statTotal">—</div>
      <div class="stat-sub">seluruh program</div>
    </div>
    <div class="stat-card terisi">
      <div class="stat-label">Sudah Terisi</div>
      <div class="stat-value" id="statTerisi">—</div>
      <div class="stat-sub">eviden tersedia</div>
    </div>
    <div class="stat-card kosong">
      <div class="stat-label">Masih Kosong</div>
      <div class="stat-value" id="statKosong">—</div>
      <div class="stat-sub">belum ada eviden</div>
    </div>
    <div class="stat-card pct">
      <div class="stat-label">Kelengkapan Eviden</div>
      <div class="stat-value" id="statPct">—</div>
      <div class="stat-sub">persentase terisi</div>
      <div class="stat-progress"><div class="stat-progress-fill" id="progressFill" style="width:0%"></div></div>
    </div>
    <div class="stat-card verif">
      <div class="stat-label">Sudah Diverifikasi</div>
      <div class="stat-value" id="statVerif">—</div>
      <div class="stat-sub">dari total indikator</div>
      <div class="stat-progress"><div class="stat-progress-fill" id="verifFill" style="width:0%"></div></div>
    </div>
  </div>

  <!-- FILTER BAR -->
  <div class="filter-bar">
    <span class="filter-label">Filter Eviden:</span>
    <button class="filter-btn active" onclick="setFilter('semua', this)">Semua</button>
    <button class="filter-btn" onclick="setFilter('Terisi', this)">✓ Terisi</button>
    <button class="filter-btn" onclick="setFilter('Kosong', this)">✗ Kosong</button>
    <span class="filter-label" style="margin-left:12px;">Verifikasi:</span>
    <button class="filter-btn" onclick="setFilterVerif('sudah', this)" id="fbvSudah">✔ Sudah Verif</button>
    <button class="filter-btn" onclick="setFilterVerif('belum', this)" id="fbvBelum">✘ Belum Verif</button>
    <div class="search-box">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <input type="text" placeholder="Cari indikator atau program..." oninput="cariData(this.value)">
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-wrap">
    <div id="loadingState" class="loading-state">
      <div class="loader-ring"></div>
      <div class="loading-text">Memindai Google Drive...</div>
      <div class="loading-sub">mengambil data folder & file</div>
    </div>
    <div id="tabelWrap" style="display:none; overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>Program Utama</th>
            <th>Sub-Program</th>
            <th>Indikator</th>
            <th class="center">Status Eviden</th>
            <th class="center">File</th>
            <th>Rincian Berkas</th>
            <th>Update Terakhir</th>
            <th class="center">Verifikasi Tim</th>
            <th class="center">Aksi</th>
          </tr>
        </thead>
        <tbody id="tbodyData"></tbody>
      </table>
      <div id="emptyState" class="empty-state" style="display:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <p>Tidak ada data yang cocok</p>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <div class="footer">
    <div class="footer-text">Dashboard Monitoring Eviden KDH 2026 · Kemendagri</div>
    <div class="footer-text" id="footerCount"></div>
  </div>

</div>

<script>
  var semuaData = [];
  var filterAktif = 'semua';
  var filterVerif = 'semua';
  var kataCari = '';

  window.onload = function () { muatDataDashboard(false); };

  function muatDataDashboard(paksa) {
    var btn = document.getElementById('btnRefresh');
    btn.classList.add('loading');
    document.getElementById('loadingState').style.display = 'flex';
    document.getElementById('tabelWrap').style.display = 'none';

    fetch('{{ route('dashboard.data') }}' + (paksa ? '?refresh=1' : ''), {
      headers: { 'Accept': 'application/json' }
    })
      .then(function (res) {
        return res.json().then(function (body) {
          if (!res.ok) throw new Error(body.message || 'Terjadi kesalahan server');
          return body;
        });
      })
      .then(tampilkanData)
      .catch(prosesGagal);
  }

  function tampilkanData(data) {
    semuaData = data;
    updateStatCards(data);
    renderTabel(data);

    var now = new Date();
    document.getElementById('lastUpdated').textContent =
      'Dimuat: ' + now.toLocaleDateString('id-ID') + ' ' +
      now.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});

    document.getElementById('btnRefresh').classList.remove('loading');
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('tabelWrap').style.display = 'block';
  }

  function updateStatCards(data) {
    var total   = data.length;
    var terisi  = data.filter(function(d){ return d.status === 'Terisi'; }).length;
    var kosong  = total - terisi;
    var pct     = total > 0 ? Math.round((terisi / total) * 100) : 0;
    var verif   = data.filter(function(d){ return d.terverifikasi === true; }).length;
    var verifPct = total > 0 ? Math.round((verif / total) * 100) : 0;

    document.getElementById('statTotal').textContent  = total;
    document.getElementById('statTerisi').textContent = terisi;
    document.getElementById('statKosong').textContent = kosong;
    document.getElementById('statPct').textContent    = pct + '%';
    document.getElementById('statVerif').textContent  = verif;
    document.getElementById('progressFill').style.width = pct + '%';
    document.getElementById('verifFill').style.width    = verifPct + '%';
    document.getElementById('footerCount').textContent  =
      terisi + ' terisi · ' + verif + ' terverifikasi dari ' + total + ' indikator';
  }

  function renderTabel(data) {
    var filtered = data.filter(function(d) {
      var cocokEviden = filterAktif === 'semua' || d.status === filterAktif;
      var cocokVerif  = filterVerif === 'semua'
        || (filterVerif === 'sudah' && d.terverifikasi === true)
        || (filterVerif === 'belum' && d.terverifikasi !== true);
      var cocokCari = kataCari === ''
        || d.kategori.toLowerCase().includes(kataCari)
        || d.subKategori.toLowerCase().includes(kataCari)
        || d.indikator.toLowerCase().includes(kataCari);
      return cocokEviden && cocokVerif && cocokCari;
    });

    var tbody = document.getElementById('tbodyData');
    tbody.innerHTML = '';

    if (filtered.length === 0) {
      document.getElementById('emptyState').style.display = 'flex';
      return;
    }
    document.getElementById('emptyState').style.display = 'none';

    var lastKategori = '';
    filtered.forEach(function(row, idx) {
      if (row.kategori !== lastKategori) {
        var trG = document.createElement('tr');
        trG.className = 'group-header';
        trG.innerHTML = '<td colspan="9">' + row.kategori + '</td>';
        tbody.appendChild(trG);
        lastKategori = row.kategori;
      }

      var fileClass = row.jumlah > 0 ? 'file-count' : 'file-count zero';
      var fileText  = row.jumlah > 0 ? row.jumlah : '—';

      var htmlRincian = '';
      if (row.rincian && row.rincian.length > 0) {
        htmlRincian = '<div class="rincian-list">';
        row.rincian.forEach(function(item) {
          htmlRincian += '<div class="rincian-item">'
            + '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>'
            + item + '</div>';
        });
        htmlRincian += '</div>';
      } else {
        htmlRincian = '<span class="rincian-empty">Tidak ada berkas</span>';
      }

      var isSudah = row.terverifikasi === true;
      var verifClass  = isSudah ? 'sudah' : 'belum';
      var verifIcon   = isSudah
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
      var verifLabel  = isSudah ? 'Terverifikasi' : 'Belum Diverifikasi';

      var tr = document.createElement('tr');
      tr.setAttribute('data-id', row.id);
      tr.style.animationDelay = (idx * 0.018) + 's';
      tr.innerHTML =
        '<td><span class="tag-kategori" title="' + row.kategori + '">' + row.kategori + '</span></td>' +
        '<td><span class="tag-sub">' + row.subKategori + '</span></td>' +
        '<td><span class="tag-indikator">' + row.indikator + '</span></td>' +
        '<td class="center"><span class="status-pill ' + (row.status === 'Terisi' ? 'terisi' : 'kosong') + '">'
          + '<span class="status-dot"></span>' + row.status + '</span></td>' +
        '<td class="center"><span class="' + fileClass + '">' + fileText + '</span></td>' +
        '<td class="top">' + htmlRincian + '</td>' +
        '<td><span class="update-time">' + row.update + '</span></td>' +
        '<td class="center">'
          + '<button class="btn-verif ' + verifClass + '" id="vbtn-' + row.id + '" onclick="klikVerifikasi(\'' + row.id + '\', this)">'
          + verifIcon + verifLabel + '</button></td>' +
        '<td class="center"><div class="aksi-group">'
          + (row.linkSem1
              ? '<a href="' + row.linkSem1 + '" target="_blank" class="btn-drive sem1" title="Buka eviden Semester I"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>Sem I</a>'
              : '<span class="btn-drive sem1 disabled" title="Folder Semester I tidak ditemukan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>Sem I</span>')
          + (row.link
              ? '<a href="' + row.link + '" target="_blank" class="btn-drive sem2" title="Buka eviden Semester II"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>Sem II</a>'
              : '<span class="btn-drive sem2 disabled"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>Sem II</span>')
          + '</div></td>';
      tbody.appendChild(tr);
    });
  }

  function klikVerifikasi(folderId, btn) {
    btn.classList.add('loading-verif');

    fetch('{{ url('/verifikasi') }}/' + encodeURIComponent(folderId), {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
      .then(function (res) {
        return res.json().then(function (body) {
          if (!res.ok) throw new Error(body.message || 'Terjadi kesalahan server');
          return body.terverifikasi;
        });
      })
      .then(function(isVerif) {
        // Update data lokal
        for (var i = 0; i < semuaData.length; i++) {
          if (semuaData[i].id === folderId) {
            semuaData[i].terverifikasi = isVerif;
            break;
          }
        }

        // Update tampilan tombol
        btn.classList.remove('loading-verif', 'sudah', 'belum', 'pulse-g', 'pulse-r');

        if (isVerif) {
          btn.classList.add('sudah');
          btn.innerHTML =
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>'
            + 'Terverifikasi';
          void btn.offsetWidth; // reflow untuk trigger animation ulang
          btn.classList.add('pulse-g');
        } else {
          btn.classList.add('belum');
          btn.innerHTML =
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>'
            + 'Belum Diverifikasi';
          void btn.offsetWidth;
          btn.classList.add('pulse-r');
        }

        // Update stat cards
        updateStatCards(semuaData);
      })
      .catch(function(err) {
        btn.classList.remove('loading-verif');
        alert('Gagal menyimpan verifikasi: ' + err.message);
      });
  }

  function setFilter(val, el) {
    filterAktif = val;
    document.querySelectorAll('.filter-btn').forEach(function(b){
      if (['semua','Terisi','Kosong'].some(function(v){ return b.textContent.includes(v === 'semua' ? 'Semua' : v); })) {
        b.classList.remove('active');
      }
    });
    el.classList.add('active');
    renderTabel(semuaData);
  }

  function setFilterVerif(val, el) {
    if (filterVerif === val) {
      filterVerif = 'semua';
      el.classList.remove('active');
    } else {
      filterVerif = val;
      document.getElementById('fbvSudah').classList.remove('active');
      document.getElementById('fbvBelum').classList.remove('active');
      el.classList.add('active');
    }
    renderTabel(semuaData);
  }

  function cariData(val) {
    kataCari = val.toLowerCase();
    renderTabel(semuaData);
  }

  function prosesGagal(error) {
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('tabelWrap').style.display = 'block';
    document.getElementById('tbodyData').innerHTML =
      '<tr><td colspan="9" style="text-align:center;padding:40px;color:#f43f5e;font-size:13px;">⚠ Gagal memuat data: ' + error.message + '</td></tr>';
    document.getElementById('btnRefresh').classList.remove('loading');
  }
</script>
</body>
</html>
