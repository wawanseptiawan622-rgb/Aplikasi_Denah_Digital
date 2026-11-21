<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? $config['app_name']) ?></title>
  <meta name="description" content="Denah digital interaktif lokasi dan fasilitas <?= e($config['village']) ?> dan sekitarnya, <?= e($config['district']) ?>.">
  <meta name="theme-color" content="#4f46e5">
  <link rel="icon" type="image/jpeg" href="<?= e($baseUrl) ?>assets/logo-kkn-kelompok-28.jpeg">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <link rel="stylesheet" href="<?= e($baseUrl) ?>assets/style.css?v=<?= (int) filemtime(BASE_PATH . '/public/assets/style.css') ?>">
</head>
<body class="antialiased min-h-screen flex flex-col">

  <header class="glass-header sticky top-0 z-50 no-print">
    <div class="max-w-6xl mx-auto px-4 h-16 sm:h-[72px] flex items-center gap-3">
      <a href="<?= e($baseUrl) ?>" class="flex items-center gap-3 min-w-0 flex-1" aria-label="Beranda <?= e($config['app_name']) ?>">
        <span class="logo-tile shrink-0">
          <img src="<?= e($baseUrl) ?>assets/logo-universitas-tangerang-raya.png" alt="Logo Universitas Tangerang Raya" class="h-9 w-9 sm:h-10 sm:w-10 object-contain">
        </span>
        <span class="min-w-0">
          <span class="block truncate text-sm sm:text-base font-extrabold leading-tight text-slate-900"><?= e($config['village']) ?></span>
          <span class="block truncate text-[11px] sm:text-xs text-slate-500"><?= e($config['district']) ?></span>
        </span>
      </a>
      <nav class="hidden md:flex items-center gap-1" aria-label="Navigasi halaman">
        <a href="#peta" class="nav-link">Peta</a>
        <a href="#fasilitas" class="nav-link">Fasilitas</a>
        <a href="#qr" class="nav-link">QR Code</a>
      </nav>
      <span class="logo-tile shrink-0">
        <img src="<?= e($baseUrl) ?>assets/logo-kkn-kelompok-28.jpeg" alt="Logo KKN Kelompok 28" class="h-9 w-9 sm:h-10 sm:w-10 rounded-[10px] object-contain">
      </span>
    </div>
  </header>

  <main class="flex-1">
    <?= $content ?>
  </main>

  <footer class="site-footer no-print">
    <div class="max-w-6xl mx-auto flex flex-col items-center gap-6 px-4 py-10 text-center md:flex-row md:justify-between md:text-left">
      <div class="flex items-center gap-3">
        <span class="logo-tile"><img src="<?= e($baseUrl) ?>assets/logo-universitas-tangerang-raya.png" alt="" class="h-10 w-10 object-contain"></span>
        <span class="logo-tile"><img src="<?= e($baseUrl) ?>assets/logo-kkn-kelompok-28.jpeg" alt="" class="h-10 w-10 rounded-[10px] object-contain"></span>
        <div class="text-left">
          <p class="text-sm font-bold text-white"><?= e($config['app_name']) ?></p>
          <p class="text-xs text-slate-400"><?= e($config['village']) ?>, <?= e($config['district']) ?></p>
        </div>
      </div>
      <div class="text-xs leading-relaxed text-slate-400 md:text-right">
        <p class="font-semibold text-slate-200">&copy; <?= date('Y') ?> <?= e($config['credit']) ?></p>
        <p class="mt-1">Peta &copy; kontributor OpenStreetMap &amp; Esri</p>
        <p class="creator-credit mt-2 text-[9px] text-slate-400/40">azril septiawan · nabila fauziah · tegar fikri maulana</p>
      </div>
    </div>
  </footer>

  <?php
    $complaintContacts = [
        ['name' => 'Pak Karna', 'role' => 'Ketua RT 08', 'phone' => '+62 895-3317-58740'],
        ['name' => 'Bu Lia', 'role' => 'Ketua RT 09', 'phone' => '+62 859-2135-5173'],
        ['name' => 'Pak Ison', 'role' => 'Ketua RT 07', 'phone' => '+62 896-7596-3640'],
        ['name' => 'Pak Suhendi', 'role' => 'Kasi Pemerintahan Tapos', 'phone' => '+62 852-1123-4308'],
    ];
    $complaintMessage = 'Assalamu\'alaikum. Saya ingin menyampaikan pengaduan. Jenis pengaduan: [tuliskan pengaduan]. Lokasi kejadian: [tuliskan lokasi]. Mohon bantuannya. Terima kasih.';
  ?>
  <div id="complaint-widget" class="fixed bottom-5 right-5 z-[1000] flex flex-col items-center gap-1.5 no-print">
    <div id="complaint-menu" class="complaint-menu hidden" role="dialog" aria-labelledby="complaint-menu-title">
      <div class="complaint-menu-sheet">
        <div class="flex items-start justify-between gap-3">
          <div>
            <h2 id="complaint-menu-title" class="text-sm font-extrabold text-slate-900">Pilih kontak pengaduan</h2>
            <p class="mt-1 text-xs text-slate-500">Pilih pengurus sesuai wilayah atau keperluan.</p>
          </div>
          <button id="close-complaint-menu" type="button" class="complaint-menu-close" aria-label="Tutup pilihan kontak">&times;</button>
        </div>
        <div class="mt-3 grid gap-2">
          <?php foreach ($complaintContacts as $contact):
            $phone = preg_replace('/\D+/', '', $contact['phone']);
            $whatsappUrl = 'https://wa.me/' . $phone . '?text=' . rawurlencode($complaintMessage);
          ?>
            <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener" class="complaint-contact">
              <span class="min-w-0">
                <span class="block text-sm font-bold text-slate-800"><?= e($contact['name']) ?></span>
                <span class="block text-xs text-slate-500"><?= e($contact['role']) ?></span>
              </span>
              <span class="shrink-0 text-[11px] font-semibold text-emerald-700"><?= e($contact['phone']) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="flex items-center gap-2 pr-1">
      <span class="complaint-label">Pengaduan Desa</span>
      <button id="hide-complaint-widget" type="button" class="grid h-8 w-8 place-items-center rounded-full border border-slate-200 bg-white/95 text-slate-500 shadow-md backdrop-blur transition hover:rotate-90 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-600" aria-label="Sembunyikan tombol pengaduan" title="Sembunyikan">
        <svg aria-hidden="true" class="h-4 w-4" viewBox="0 0 20 20" fill="none">
          <path d="m5 5 10 10M15 5 5 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </button>
    </div>
    <button id="complaint-whatsapp-link" type="button"
      class="complaint-fab grid h-14 w-14 touch-none cursor-grab place-items-center rounded-full text-white ring-4 ring-white transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 active:cursor-grabbing"
      aria-label="Pilih kontak untuk pengaduan melalui WhatsApp" aria-haspopup="dialog" aria-expanded="false" aria-controls="complaint-menu"
      title="Pilih kontak pengaduan">
      <svg aria-hidden="true" class="h-8 w-8 fill-current" viewBox="0 0 24 24">
        <path d="M20.52 3.48A11.78 11.78 0 0 0 12.13 0C5.62 0 .32 5.3.32 11.81c0 2.08.54 4.1 1.57 5.89L.22 24l6.45-1.69a11.8 11.8 0 0 0 5.46 1.35h.01c6.51 0 11.81-5.3 11.81-11.81 0-3.16-1.23-6.13-3.43-8.37ZM12.14 21.65h-.01c-1.71 0-3.38-.46-4.84-1.33l-.35-.21-3.83 1 1.02-3.73-.23-.38a9.78 9.78 0 0 1-1.5-5.19c0-5.37 4.37-9.74 9.75-9.74a9.68 9.68 0 0 1 6.9 2.86 9.68 9.68 0 0 1 2.85 6.91c0 5.37-4.37 9.74-9.76 9.74Zm5.35-7.3c-.29-.14-1.7-.84-1.96-.94-.26-.1-.45-.14-.64.14-.19.29-.74.94-.9 1.13-.17.19-.33.22-.62.08-.29-.14-1.21-.45-2.31-1.43-.85-.76-1.43-1.7-1.6-1.99-.16-.29-.02-.44.12-.58.13-.13.29-.33.43-.5.14-.17.19-.29.29-.48.1-.19.05-.36-.02-.5-.08-.14-.64-1.55-.88-2.12-.23-.56-.47-.49-.64-.5h-.54c-.19 0-.5.07-.76.36-.26.29-1 1-1 2.42s1.02 2.8 1.16 2.99c.14.19 2 3.05 4.85 4.28.68.29 1.21.47 1.62.6.68.21 1.3.18 1.79.11.55-.08 1.7-.7 1.94-1.38.24-.67.24-1.24.17-1.36-.07-.12-.26-.19-.55-.33Z"/>
      </svg>
    </button>
  </div>
  <button id="show-complaint-widget" type="button"
    class="fixed right-0 bottom-8 z-[1000] hidden h-12 w-10 touch-none cursor-grab place-items-center rounded-l-xl border border-r-0 border-emerald-200 bg-white text-emerald-800 shadow-lg hover:bg-emerald-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-emerald-600 active:cursor-grabbing no-print"
    aria-label="Tampilkan kembali Pengaduan Desa. Seret untuk memindahkan panah; gunakan tombol panah pada keyboard untuk menggeser." title="Tampilkan Pengaduan Desa">
    <svg aria-hidden="true" class="h-6 w-6" viewBox="0 0 24 24" fill="none">
      <path d="m14.5 5-7 7 7 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </button>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <?php if (!empty($pageScript)): ?>
  <script src="<?= e($baseUrl) ?>assets/<?= e($pageScript) ?>?v=<?= (int) filemtime(BASE_PATH . '/public/assets/' . $pageScript) ?>"></script>
  <?php endif; ?>
  <script>
    (() => {
      const widget = document.getElementById('complaint-widget');
      const link = document.getElementById('complaint-whatsapp-link');
      const menu = document.getElementById('complaint-menu');
      const closeMenuButton = document.getElementById('close-complaint-menu');
      const hideButton = document.getElementById('hide-complaint-widget');
      const showButton = document.getElementById('show-complaint-widget');
      let pointerStart = null;
      let showPointerStart = null;
      let dragged = false;
      let showDragged = false;
      let suppressClick = false;
      let suppressShowClick = false;
      let hideTimer = null;
      let menuCloseTimer = null;
      let dockSide = 'right';

      const MENU_CLOSE_MS = 180; // samakan dengan durasi complaint-menu-out di CSS

      function isMenuOpen() {
        return !menu.classList.contains('hidden') && !menu.classList.contains('is-closing');
      }

      function setComplaintMenuOpen(open) {
        window.clearTimeout(menuCloseTimer);

        if (open) {
          menu.classList.remove('hidden', 'is-closing');
          link.classList.add('is-open');
          link.setAttribute('aria-expanded', 'true');
          menu.querySelector('a').focus({ preventScroll: true });
          return;
        }

        if (menu.classList.contains('hidden')) return;

        link.classList.remove('is-open');
        link.setAttribute('aria-expanded', 'false');
        menu.classList.add('is-closing');
        menuCloseTimer = window.setTimeout(() => {
          menu.classList.add('hidden');
          menu.classList.remove('is-closing');
        }, MENU_CLOSE_MS);
      }

      link.addEventListener('click', () => {
        if (suppressClick) return;
        setComplaintMenuOpen(!isMenuOpen());
      });

      closeMenuButton.addEventListener('click', () => {
        setComplaintMenuOpen(false);
        link.focus();
      });

      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isMenuOpen()) {
          setComplaintMenuOpen(false);
          link.focus();
        }
      });

      document.addEventListener('click', (event) => {
        if (event.target === menu) {
          setComplaintMenuOpen(false);
          return;
        }
        if (isMenuOpen() && !widget.contains(event.target)) {
          setComplaintMenuOpen(false);
        }
      });

      function dockElement(element, side, top) {
        const maxTop = Math.max(0, window.innerHeight - element.offsetHeight);
        element.style.left = side === 'left' ? '0px' : 'auto';
        element.style.right = side === 'right' ? '0px' : 'auto';
        element.style.top = `${Math.max(0, Math.min(maxTop, top))}px`;
        element.style.bottom = 'auto';
      }

      function moveElement(element, left, top) {
        const maxLeft = Math.max(0, window.innerWidth - element.offsetWidth);
        const maxTop = Math.max(0, window.innerHeight - element.offsetHeight);
        element.style.left = `${Math.max(0, Math.min(maxLeft, left))}px`;
        element.style.top = `${Math.max(0, Math.min(maxTop, top))}px`;
        element.style.right = 'auto';
        element.style.bottom = 'auto';
      }

      function setArrowSide(side) {
        showButton.classList.toggle('rounded-l-xl', side === 'right');
        showButton.classList.toggle('rounded-r-xl', side === 'left');
        showButton.classList.toggle('border-r-0', side === 'right');
        showButton.classList.toggle('border-l-0', side === 'left');
        showButton.querySelector('path').setAttribute(
          'd',
          side === 'right' ? 'm14.5 5-7 7 7 7' : 'm9.5 5 7 7-7 7'
        );
      }

      hideButton.addEventListener('click', () => {
        const bounds = widget.getBoundingClientRect();
        dockSide = bounds.left + bounds.width / 2 < window.innerWidth / 2 ? 'left' : 'right';
        dockElement(showButton, dockSide, bounds.top + (bounds.height - showButton.offsetHeight) / 2);
        setArrowSide(dockSide);
        widget.style.setProperty(
          '--complaint-hide-direction',
          dockSide === 'left' ? 'translateX(calc(-100vw - 100%))' : 'translateX(calc(100vw + 100%))'
        );
        hideButton.disabled = true;
        widget.classList.add('is-sliding-out');
        hideTimer = window.setTimeout(() => {
          widget.classList.add('hidden');
          widget.classList.remove('is-sliding-out');
          showButton.classList.remove('hidden');
          showButton.classList.add('grid');
          showButton.focus();
          hideButton.disabled = false;
          hideTimer = null;
        }, 2000);
      });

      showButton.addEventListener('click', () => {
        if (suppressShowClick) return;
        if (hideTimer) {
          window.clearTimeout(hideTimer);
          hideTimer = null;
        }
        showButton.classList.add('hidden');
        showButton.classList.remove('grid');
        widget.classList.remove('hidden');
        link.focus();
      });

      showButton.addEventListener('pointerdown', (event) => {
        if (event.button !== 0) return;
        const bounds = showButton.getBoundingClientRect();
        showPointerStart = {
          id: event.pointerId,
          x: event.clientX,
          y: event.clientY,
          left: bounds.left,
          top: bounds.top
        };
        showDragged = false;
      });

      window.addEventListener('pointermove', (event) => {
        if (!showPointerStart || event.pointerId !== showPointerStart.id) return;
        const deltaX = event.clientX - showPointerStart.x;
        const deltaY = event.clientY - showPointerStart.y;
        if (!showDragged && Math.hypot(deltaX, deltaY) < 6) return;

        showDragged = true;
        dockSide = event.clientX < window.innerWidth / 2 ? 'left' : 'right';
        dockElement(showButton, dockSide, showPointerStart.top + deltaY);
        setArrowSide(dockSide);
      });

      window.addEventListener('pointerup', (event) => {
        if (!showPointerStart || event.pointerId !== showPointerStart.id) return;
        if (showDragged) {
          suppressShowClick = true;
          window.setTimeout(() => { suppressShowClick = false; }, 0);
        }
        showPointerStart = null;
      });

      showButton.addEventListener('keydown', (event) => {
        const steps = { ArrowUp: [0, -20], ArrowDown: [0, 20], ArrowLeft: [-1, 0], ArrowRight: [1, 0] };
        const step = steps[event.key];
        if (!step) return;
        event.preventDefault();
        const bounds = showButton.getBoundingClientRect();
        if (step[0]) dockSide = step[0] < 0 ? 'left' : 'right';
        dockElement(showButton, dockSide, bounds.top + step[1]);
        setArrowSide(dockSide);
      });

      link.addEventListener('pointerdown', (event) => {
        if (event.button !== 0) return;
        const bounds = widget.getBoundingClientRect();
        pointerStart = {
          id: event.pointerId,
          x: event.clientX,
          y: event.clientY,
          left: bounds.left,
          top: bounds.top
        };
        dragged = false;
      });

      window.addEventListener('pointermove', (event) => {
        if (!pointerStart || event.pointerId !== pointerStart.id) return;
        const deltaX = event.clientX - pointerStart.x;
        const deltaY = event.clientY - pointerStart.y;
        if (!dragged && Math.hypot(deltaX, deltaY) < 6) return;

        dragged = true;
        moveElement(widget, pointerStart.left + deltaX, pointerStart.top + deltaY);
      });

      window.addEventListener('pointerup', (event) => {
        if (!pointerStart || event.pointerId !== pointerStart.id) return;
        if (dragged) {
          suppressClick = true;
          window.setTimeout(() => { suppressClick = false; }, 0);
        }
        pointerStart = null;
      });

      link.addEventListener('click', (event) => {
        if (!suppressClick) return;
        event.preventDefault();
        event.stopPropagation();
      });

      link.addEventListener('keydown', (event) => {
        const steps = { ArrowUp: [0, -20], ArrowDown: [0, 20], ArrowLeft: [-20, 0], ArrowRight: [20, 0] };
        const step = steps[event.key];
        if (!step) return;
        event.preventDefault();
        const bounds = widget.getBoundingClientRect();
        moveElement(widget, bounds.left + step[0], bounds.top + step[1]);
      });

      window.addEventListener('resize', () => {
        if (!widget.classList.contains('hidden')) {
          const bounds = widget.getBoundingClientRect();
          if (widget.style.left) moveElement(widget, bounds.left, bounds.top);
        }
        if (!showButton.classList.contains('hidden')) {
          const bounds = showButton.getBoundingClientRect();
          dockElement(showButton, dockSide, bounds.top);
        }
      });
    })();
  </script>
</body>
</html>
