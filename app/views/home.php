<?php
$pageScript = 'app.js';
$facCount   = count($facilities);
$catCount   = count($categories);
?>

<!-- ============ HERO ============ -->
<section class="max-w-6xl mx-auto px-4 pt-8 sm:pt-14 no-print">
  <div class="grid items-center gap-8 lg:grid-cols-[1.2fr_1fr] lg:gap-12">
    <div class="reveal">
      <span class="eyebrow"><span class="dot"></span>Denah Digital Warga</span>
      <h1 class="mt-5 text-[2rem] font-extrabold leading-[1.12] tracking-tight text-slate-900 sm:text-5xl">
        Temukan lokasi penting di <span class="gradient-text">lingkungan Anda</span>
      </h1>
      <p class="mt-4 max-w-xl text-[15px] leading-relaxed text-slate-600 sm:text-base">
        Peta interaktif lokasi dan fasilitas <?= e($config['village']) ?> dan sekitarnya, <?= e($config['district']) ?>.
        Temukan fasilitas pendidikan, tempat ibadah, layanan warga, dan lokasi penting lainnya lengkap dengan petunjuk arah.
      </p>
      <div class="mt-7 flex flex-wrap gap-3">
        <a href="#peta" class="btn btn-primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 20 3 17V4l6 3m0 13 6-3m-6 3V7m6 10 6 3V7l-6-3m0 13V4M9 7l6-3"/></svg>
          Jelajahi Peta
        </a>
        <a href="#qr" class="btn btn-ghost">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h3v3h-3zM20 14v.01M14 20h.01M20 20h.01M17 17v3"/></svg>
          Scan QR Code
        </a>
      </div>
    </div>

    <div class="reveal d2">
      <div class="hero-card">
        <div class="flex items-start gap-3">
          <span class="hero-pin" aria-hidden="true">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>
          </span>
          <div class="min-w-0">
            <p class="text-[11px] font-bold uppercase tracking-[.14em] text-indigo-200">Wilayah pemetaan</p>
            <p class="mt-1 text-xl font-extrabold leading-snug"><?= e($config['village']) ?></p>
            <p class="text-sm text-indigo-200"><?= e($config['district']) ?></p>
          </div>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-3">
          <div class="hero-stat">
            <p class="text-3xl font-extrabold leading-none"><?= $facCount ?></p>
            <p class="mt-1.5 text-xs font-medium text-indigo-100">Lokasi terdata</p>
          </div>
          <div class="hero-stat">
            <p class="text-3xl font-extrabold leading-none"><?= $catCount ?></p>
            <p class="mt-1.5 text-xs font-medium text-indigo-100">Kategori fasilitas</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ PETA INTERAKTIF ============ -->
<section id="peta" data-motion class="max-w-6xl mx-auto px-3 sm:px-4 mt-12 sm:mt-16 no-print">
  <div class="mb-4 px-1">
    <p class="text-xs font-bold uppercase tracking-[.14em] text-blue-600">Denah lingkungan</p>
    <h2 class="mt-1 text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Cari lokasi di sekitar Anda</h2>
    <p class="mt-1.5 max-w-2xl text-sm leading-relaxed text-slate-600">
      Ketuk penanda di peta untuk melihat detail, atau pilih kategori untuk menyaring lokasi.
    </p>
  </div>

  <div class="card map-card">
    <!-- Filter kategori (sekaligus legenda) -->
    <div class="chip-bar no-scrollbar flex gap-2 overflow-x-auto px-3 py-3 sm:flex-wrap sm:px-4" role="group" aria-label="Navigasi kategori fasilitas">
      <button type="button" class="chip" data-category-filter="" aria-pressed="true">
        <span aria-hidden="true">🗺️</span><span>Semua</span>
      </button>
      <?php foreach ($categories as $categoryKey => $c): ?>
        <button type="button" class="chip" data-category-filter="<?= e($categoryKey) ?>" aria-pressed="false"
          style="--c:<?= e($c['color']) ?>">
          <span aria-hidden="true"><?= $c['icon'] ?></span>
          <span><?= e($c['label']) ?></span>
        </button>
      <?php endforeach; ?>
    </div>

    <div class="relative">
      <div id="map" aria-label="Peta denah interaktif"></div>

      <div class="seg absolute left-3 top-3 z-[500]" role="group" aria-label="Pilih tampilan peta">
        <button id="btn-map-satellite" type="button" class="seg-btn" aria-pressed="true">Satelit</button>
        <button id="btn-map-street" type="button" class="seg-btn" aria-pressed="false">Peta jalan</button>
      </div>

      <button id="btn-recenter" type="button" class="map-fab absolute right-3 top-3 z-[500]" aria-label="Pusatkan kembali peta">
        <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
        <span>Pusatkan</span>
      </button>
      <p id="map-navigation-status" class="map-status absolute right-3 top-[66px] z-[500] hidden max-w-[70%]" role="status" aria-live="polite"></p>
    </div>
  </div>

  <section data-motion class="location-assistant card mt-4 p-4 sm:p-5" aria-labelledby="location-assistant-title">
    <div class="flex items-start gap-3">
      <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-emerald-50 text-xl" aria-hidden="true">🧭</span>
      <div>
        <h3 id="location-assistant-title" class="text-base font-extrabold text-slate-900">Asisten Lokasi</h3>
        <p class="mt-1 text-sm leading-relaxed text-slate-600">
          Tulis tujuan atau kategori, lalu izinkan akses lokasi untuk menemukan fasilitas terdekat.
        </p>
      </div>
    </div>
    <form id="location-assistant-form" class="mt-4 flex flex-col gap-2 sm:flex-row">
      <label for="location-assistant-query" class="sr-only">Tujuan atau kategori lokasi</label>
      <input id="location-assistant-query" type="search" autocomplete="off"
        class="search-input min-w-0 flex-1"
        placeholder="Contoh: masjid terdekat atau rumah ketua RT">
      <button type="submit" class="btn btn-primary shrink-0">Cari terdekat</button>
    </form>
    <p class="mt-2 text-xs leading-relaxed text-slate-500">
      Jarak yang dibandingkan adalah garis lurus dari posisi Anda. Posisi diproses di perangkat ini; Google Maps dibuka hanya saat Anda memilih tombol navigasi.
    </p>
    <p id="location-assistant-status" class="mt-3 text-sm text-slate-600" role="status" aria-live="polite">
      Contoh: “masjid terdekat”, “pos ronda”, atau “ketua RT”.
    </p>
    <div id="location-assistant-results" class="mt-3 hidden" aria-live="polite"></div>
  </section>

  <dialog id="street-view-dialog" class="street-view-dialog" aria-labelledby="street-view-title">
    <div class="street-view-dialog-header">
      <div class="min-w-0">
        <p class="street-view-eyebrow">Google Street View</p>
        <h3 id="street-view-title" class="street-view-dialog-title">Tampilan lokasi</h3>
      </div>
      <div class="street-view-dialog-actions">
        <a id="street-view-external" href="#" target="_blank" rel="noopener" class="street-view-open-link">Buka di Google Maps</a>
        <button id="street-view-close" type="button" class="street-view-close" aria-label="Tutup Street View">Tutup</button>
      </div>
    </div>
    <iframe id="street-view-frame" class="street-view-frame" title="Panorama Street View lokasi"
      allow="accelerometer; gyroscope; fullscreen" referrerpolicy="strict-origin-when-cross-origin"></iframe>
  </dialog>
</section>

<!-- ============ DAFTAR FASILITAS ============ -->
<section id="fasilitas" data-motion class="max-w-6xl mx-auto px-4 mt-12 sm:mt-16 no-print">
  <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
    <div>
      <p class="text-xs font-bold uppercase tracking-[.14em] text-emerald-600">Direktori</p>
      <h2 class="mt-1 text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Lokasi yang Ada di Peta</h2>
    </div>
    <span class="count-pill"><?= $facCount ?> lokasi</span>
  </div>
  <p class="mb-4 max-w-2xl text-sm leading-relaxed text-slate-600">
    Semua lokasi di bawah ini sudah ditandai pada peta interaktif. Ketuk kartu untuk melihat titiknya. Ketik untuk mencari, lalu geser kartu ke samping untuk melihat lokasi lainnya.
  </p>
  <p class="mb-4 text-xs text-slate-500">
    Data lokasi diperbarui
    <time datetime="<?= e(date(DATE_ATOM, $dataUpdatedAt)) ?>"><?= e(date('d/m/Y', $dataUpdatedAt)) ?></time>
  </p>

  <?php if ($facilities): ?>
    <label for="facility-search" class="sr-only">Cari nama atau jenis fasilitas</label>
    <div class="search-wrap mb-4">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
      <input id="facility-search" type="search" autocomplete="off" class="search-input"
        placeholder="Cari, misalnya: masjid atau posyandu">
    </div>
    <p id="facility-search-status" class="sr-only" role="status" aria-live="polite"></p>
    <p id="facility-navigation-status" class="mb-3 hidden rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-medium text-blue-900" role="status" aria-live="polite"></p>

    <div id="facility-list" class="flex snap-x snap-mandatory gap-4 overflow-x-auto"
      role="region" aria-label="Daftar lokasi, geser ke samping untuk melihat lainnya" tabindex="0">
      <?php $facilityIndex = 0; foreach ($facilities as $f): $cat = $categories[$f['category']] ?? null; ?>
        <button type="button" data-focus="<?= (int)$f['id'] ?>"
          data-motion="card"
          aria-label="Tampilkan <?= e($f['name']) ?> di peta"
          style="--c:<?= e($cat['color'] ?? '#64748b') ?>;--motion-order:<?= min($facilityIndex++, 6) ?>"
          class="fac-card flex min-h-[132px] w-[86%] shrink-0 snap-start items-start gap-3.5 p-4 pt-5 text-left sm:w-[calc((100%-1rem)/2)] lg:w-[calc((100%-2rem)/3)]">
          <span class="fac-icon" aria-hidden="true"><?= e($f['icon'] ?? $cat['icon'] ?? '📍') ?></span>
          <span class="relative z-[1] min-w-0 flex-1">
            <span class="flex flex-wrap items-center gap-1.5">
              <span class="fac-badge"><?= e($cat['label'] ?? 'Fasilitas') ?></span>
              <span class="inline-flex items-center gap-1 rounded px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50">
                <span aria-hidden="true">📍</span> Ditandai di peta
              </span>
            </span>
            <span class="mt-1.5 block text-[15px] font-bold leading-snug text-slate-900"><?= e($f['name']) ?></span>
            <span class="mt-1 block text-[13px] leading-relaxed text-slate-600 line-clamp-2"><?= e($f['description']) ?></span>
          </span>
          <span class="fac-arrow" aria-hidden="true">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6"/></svg>
          </span>
        </button>
      <?php endforeach; ?>
    </div>
    <p id="facility-empty" class="hidden rounded-2xl border border-dashed border-slate-300 bg-white/80 px-4 py-8 text-center text-sm text-slate-600">
      Fasilitas tidak ditemukan. Coba kata pencarian lain.
    </p>
  <?php else: ?>
    <p class="card p-5 text-sm text-slate-600">
      Belum ada fasilitas yang ditampilkan. Silakan kembali lagi nanti.
    </p>
  <?php endif; ?>
</section>

<!-- ============ QR CODE ============ -->
<section id="qr" data-motion class="max-w-6xl mx-auto px-4 py-12 sm:py-16">
  <div id="qr-print" class="card qr-card flex flex-col items-center gap-7 p-6 sm:p-10 md:flex-row md:gap-14">
    <div class="qr-frame shrink-0">
      <i></i><i></i><i></i><i></i>
      <div id="qrcode" class="grid h-[min(230px,64vw)] w-[min(230px,64vw)] place-items-center"></div>
    </div>

    <div class="text-center md:text-left">
      <p class="text-xs font-bold uppercase tracking-[.14em] text-emerald-600">Untuk warga &amp; tamu</p>
      <h2 class="mt-1.5 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Scan QR Code Denah Ini</h2>
      <p class="mt-3 max-w-md text-sm leading-relaxed text-slate-600 sm:text-[15px]">
        Arahkan kamera HP ke kode ini untuk membuka denah interaktif
        <?= e($config['village']) ?> dan sekitarnya, <?= e($config['district']) ?>.
      </p>
      <p class="mt-3 inline-block max-w-full break-all rounded-lg bg-slate-100 px-3 py-1.5 font-mono text-[11px] text-slate-500" id="qr-url"><?= e($baseUrl) ?></p>

      <div class="no-print mt-6 flex flex-wrap justify-center gap-2.5 md:justify-start">
        <button id="btn-download" type="button" class="btn btn-primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11m0 0 4-4m-4 4-4-4M5 20h14"/></svg>
          <span>Unduh Poster PNG</span>
        </button>
        <button id="btn-print" type="button" class="btn btn-ghost">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 9V4h10v5M7 17H5a1 1 0 0 1-1-1v-5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a1 1 0 0 1-1 1h-2M7 14h10v6H7z"/></svg>
          <span>Cetak</span>
        </button>
        <button id="btn-copy" type="button" class="btn btn-ghost">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/></svg>
          <span id="btn-copy-label">Salin Link</span>
        </button>
      </div>
    </div>
  </div>
</section>

<!-- Data dari PHP ke JavaScript -->
<script>
  window.APP = {
    baseUrl: <?= json_encode($baseUrl) ?>,
    map: <?= json_encode($config['map']) ?>,
    title: <?= json_encode($config['village']) ?>,
    region: <?= json_encode($config['district']) ?>,
    credit: <?= json_encode($config['credit'], JSON_UNESCAPED_UNICODE) ?>,
    categories: <?= json_encode($categories, JSON_UNESCAPED_UNICODE) ?>,
    facilities: <?= json_encode($facilities, JSON_UNESCAPED_UNICODE) ?>
  };
</script>
