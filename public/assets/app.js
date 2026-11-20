(function () {
  const A = window.APP;
  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  const motionTargets = [...document.querySelectorAll('.reveal, [data-motion]')];
  if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    motionTargets.forEach((element) => element.classList.add('motion-target'));
    document.documentElement.classList.add('motion-ready');
    const motionObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-in-view');
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -36px 0px' });
    motionTargets.forEach((element) => motionObserver.observe(element));
  }

  /* ---------- PETA ---------- */
  const center = [A.map.lat, A.map.lng];
  const map = L.map('map', { zoomControl: false, minZoom: A.map.min_zoom, maxZoom: A.map.max_zoom })
               .setView(center, A.map.zoom);
  L.control.zoom({ position: 'bottomleft' }).addTo(map);

  const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    maxZoom: 19,
    attribution: 'Imagery &copy; Esri, Maxar, Earthstar Geographics, CNES/Airbus, USDA, USGS, AeroGRID, IGN, and GIS User Community'
  });
  const streetLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  });
  satelliteLayer.addTo(map);

  const streetViewDialog = document.getElementById('street-view-dialog');
  const streetViewFrame = document.getElementById('street-view-frame');
  const streetViewTitle = document.getElementById('street-view-title');
  const streetViewExternal = document.getElementById('street-view-external');

  function streetViewEmbedUrl(lat, lng) {
    const params = new URLSearchParams({
      layer: 'c',
      cbll: `${Number(lat)},${Number(lng)}`,
      cbp: '12,0,0,0,0',
      output: 'svembed'
    });
    return `https://maps.google.com/maps?${params}`;
  }

  function openStreetView(lat, lng, label) {
    const coordinates = `${Number(lat)},${Number(lng)}`;
    streetViewTitle.textContent = label;
    streetViewFrame.src = streetViewEmbedUrl(lat, lng);
    streetViewExternal.href = `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${encodeURIComponent(coordinates)}`;
    streetViewDialog.showModal();
  }

  streetViewDialog.addEventListener('close', () => {
    streetViewFrame.removeAttribute('src');
  });
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && streetViewDialog.open) streetViewDialog.close();
  });
  document.getElementById('street-view-close').addEventListener('click', () => streetViewDialog.close());
  streetViewDialog.addEventListener('click', (event) => {
    if (event.target === streetViewDialog) streetViewDialog.close();
  });
  document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-street-view]');
    if (!button) return;
    openStreetView(button.dataset.lat, button.dataset.lng, button.dataset.label);
  });

  const satelliteButton = document.getElementById('btn-map-satellite');
  const streetButton = document.getElementById('btn-map-street');
  function setBaseLayer(layer, activeButton, inactiveButton) {
    if (map.hasLayer(layer)) return;
    map.removeLayer(satelliteLayer);
    map.removeLayer(streetLayer);
    layer.addTo(map);
    activeButton.setAttribute('aria-pressed', 'true');
    inactiveButton.setAttribute('aria-pressed', 'false');
  }

  satelliteButton.addEventListener('click', () => setBaseLayer(satelliteLayer, satelliteButton, streetButton));
  streetButton.addEventListener('click', () => setBaseLayer(streetLayer, streetButton, satelliteButton));

  const markers = {};

  function pinIcon(cat, facilityIcon) {
    const c = A.categories[cat] || { color: '#64748b', icon: '📍' };
    return L.divIcon({
      className: '',
      html: `<div class="pin" style="background:${c.color}"><span>${esc(facilityIcon || c.icon)}</span></div>`,
      iconSize: [38, 38],
      iconAnchor: [19, 38],
      popupAnchor: [0, -38]
    });
  }

  function popupHtml(f) {
    const c = A.categories[f.category] || { label: 'Lainnya', color: '#64748b', icon: '📍' };
    const destination = encodeURIComponent(`${f.lat},${f.lng}`);
    const gmaps = `https://www.google.com/maps/dir/?api=1&destination=${destination}`;
    return `
      <article class="facility-popup">
        <header class="facility-popup-header">
          <span class="facility-popup-icon" aria-hidden="true">${esc(f.icon || c.icon)}</span>
          <div class="min-w-0">
            <span class="facility-popup-category" style="--category-color:${c.color}">${esc(c.label)}</span>
            <h3 class="facility-popup-title">${esc(f.name)}</h3>
          </div>
        </header>
        <div class="facility-popup-body">
          <p class="facility-popup-description">${esc(f.description)}</p>
          <p class="facility-popup-address">
            <span class="facility-popup-address-label">Alamat</span>
            ${esc(f.address || 'Belum tersedia')}
          </p>
          <p class="facility-popup-directions-note">Lihat rute menuju lokasi ini di Google Maps.</p>
          <div class="facility-popup-actions">
            <a href="${gmaps}" target="_blank" rel="noopener"
               class="facility-popup-directions">Buka rute di Google Maps</a>
            <button type="button" data-street-view data-lat="${Number(f.lat)}" data-lng="${Number(f.lng)}"
               data-label="${esc(f.name)}" class="facility-popup-streetview">Lihat Street View</button>
          </div>
        </div>
      </article>`;
  }

  A.facilities.forEach((f) => {
    markers[f.id] = L.marker([f.lat, f.lng], { icon: pinIcon(f.category, f.icon), title: f.name })
      .addTo(map)
      .bindPopup(popupHtml(f), { closeButton: true, autoPanPadding: [20, 20], maxWidth: 300 });
  });

  const assistantForm = document.getElementById('location-assistant-form');
  const assistantQuery = document.getElementById('location-assistant-query');
  const assistantStatus = document.getElementById('location-assistant-status');
  const assistantResults = document.getElementById('location-assistant-results');
  let userLocationLayer = null;
  let proximityLine = null;

  function straightLineDistanceKm(origin, destination) {
    const radians = (degrees) => degrees * Math.PI / 180;
    const latitudeDifference = radians(Number(destination.lat) - origin.lat);
    const longitudeDifference = radians(Number(destination.lng) - origin.lng);
    const haversine = Math.min(1, Math.sin(latitudeDifference / 2) ** 2 +
      Math.cos(radians(origin.lat)) * Math.cos(radians(Number(destination.lat))) *
      Math.sin(longitudeDifference / 2) ** 2);
    return 6371 * 2 * Math.atan2(Math.sqrt(haversine), Math.sqrt(1 - haversine));
  }

  function formatDistance(kilometers) {
    if (kilometers < 1) return `${Math.round(kilometers * 1000)} m`;
    return `${kilometers.toLocaleString('id-ID', { maximumFractionDigits: 2 })} km`;
  }

  function matchingFacilities(query) {
    const ignoredWords = new Set([
      'saya', 'aku', 'mau', 'ingin', 'ke', 'di', 'terdekat', 'dekat', 'lokasi',
      'tempat', 'fasilitas', 'cari', 'carikan', 'tolong', 'tunjukkan', 'tunjukin',
      'arah', 'rute', 'menuju', 'ada', 'yang', 'dan', 'untuk', 'paling', 'jalan',
      'sini', 'sekitar', 'sekitaran'
    ]);
    const terms = [...new Set((query.toLocaleLowerCase('id').match(/[a-z0-9]+/g) || [])
      .filter((term) => term.length > 1 && !ignoredWords.has(term)))];

    if (!terms.length) return A.facilities;

    return A.facilities.filter((facility) => {
      const category = A.categories[facility.category];
      const searchableText = [
        facility.name,
        facility.description,
        facility.address,
        category && category.label
      ].filter(Boolean).join(' ').toLocaleLowerCase('id');
      return terms.every((term) => searchableText.includes(term));
    });
  }

  if (assistantForm) {
    assistantForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      if (!navigator.geolocation) {
        assistantStatus.textContent = 'Browser ini tidak mendukung akses lokasi. Coba browser lain atau perangkat yang memiliki GPS.';
        assistantResults.classList.add('hidden');
        return;
      }

      const submitButton = assistantForm.querySelector('button[type="submit"]');
      submitButton.disabled = true;
      assistantStatus.textContent = 'Meminta izin lokasi dari perangkat Anda...';
      assistantResults.classList.add('hidden');

      let position;
      try {
        position = await new Promise((resolve, reject) => {
          navigator.geolocation.getCurrentPosition(resolve, reject, {
            enableHighAccuracy: true,
            maximumAge: 60000,
            timeout: 15000
          });
        });
      } catch (error) {
        if (error.code === 1) {
          assistantStatus.textContent = 'Izin lokasi ditolak. Izinkan akses lokasi untuk situs ini, lalu coba lagi.';
        } else if (error.code === 2) {
          assistantStatus.textContent = 'Posisi tidak dapat ditemukan. Pastikan GPS atau layanan lokasi perangkat aktif.';
        } else if (error.code === 3) {
          assistantStatus.textContent = 'Permintaan lokasi melewati batas waktu. Periksa GPS dan coba lagi.';
        } else {
          assistantStatus.textContent = 'Lokasi tidak dapat diakses saat ini. Pastikan halaman dibuka melalui HTTPS atau localhost, lalu coba lagi.';
        }
        submitButton.disabled = false;
        return;
      }

      submitButton.disabled = false;
      const origin = {
        lat: position.coords.latitude,
        lng: position.coords.longitude
      };
      const matches = matchingFacilities(assistantQuery.value);

      if (!matches.length) {
        assistantStatus.textContent = 'Belum ada lokasi yang cocok dengan tujuan tersebut. Coba nama lokasi atau kategori lain.';
        return;
      }

      const nearestFacilities = matches
        .map((facility) => ({ ...facility, distanceKm: straightLineDistanceKm(origin, facility) }))
        .sort((first, second) => first.distanceKm - second.distanceKm)
        .slice(0, 3);

      if (userLocationLayer) map.removeLayer(userLocationLayer);
      userLocationLayer = L.circleMarker([origin.lat, origin.lng], {
        radius: 9,
        color: '#fff',
        weight: 3,
        fillColor: '#176b68',
        fillOpacity: 1
      }).addTo(map).bindTooltip('Posisi Anda');

      const hasSearch = assistantQuery.value.trim().length > 0;
      assistantStatus.textContent = hasSearch
        ? `Lokasi terdekat yang cocok dengan “${assistantQuery.value.trim()}” (diurutkan berdasarkan jarak garis lurus):`
        : 'Tiga fasilitas terdekat dari posisi Anda (berdasarkan jarak garis lurus):';

      assistantResults.innerHTML = nearestFacilities.map((facility, index) => {
        const category = A.categories[facility.category] || { label: 'Lokasi' };
        const route = new URLSearchParams({
          api: '1',
          origin: `${origin.lat},${origin.lng}`,
          destination: `${facility.lat},${facility.lng}`,
          travelmode: 'driving'
        });
        const mapsUrl = `https://www.google.com/maps/dir/?${route.toString()}`;
        return `
          <article class="location-result">
            <div class="location-result-heading">
              <span class="location-result-rank">${index + 1}</span>
              <div class="min-w-0 flex-1">
                <p class="location-result-name">${esc(facility.name)}</p>
                <p class="location-result-meta">${esc(category.label)} · ${formatDistance(facility.distanceKm)} garis lurus</p>
              </div>
            </div>
            <div class="location-result-actions">
              <button type="button" class="location-result-map" data-assistant-focus="${Number(facility.id)}">Tampilkan di peta</button>
              <a class="location-result-route" href="${mapsUrl}" target="_blank" rel="noopener">Navigasi Google Maps</a>
            </div>
          </article>`;
      }).join('');
      assistantResults.classList.remove('hidden');
    });

    assistantResults.addEventListener('click', (event) => {
      const button = event.target.closest('[data-assistant-focus]');
      if (!button) return;

      const facility = A.facilities.find((item) => String(item.id) === button.dataset.assistantFocus);
      const marker = facility && markers[facility.id];
      if (!facility || !marker || !userLocationLayer) return;

      if (selectedCategory) activateCategory('');
      if (searchInput && searchInput.value) {
        searchInput.value = '';
        applyFacilityFilters();
      }

      if (proximityLine) map.removeLayer(proximityLine);
      const location = userLocationLayer.getLatLng();
      const destination = marker.getLatLng();
      proximityLine = L.polyline([location, destination], {
        color: '#176b68',
        weight: 4,
        dashArray: '8 8',
        opacity: 0.8
      }).addTo(map);
      map.fitBounds(L.latLngBounds([location, destination]), { padding: [48, 48], maxZoom: 17 });
      map.once('moveend', () => marker.openPopup());
    });
  }

  const allFacilityMarkers = Object.values(markers);
  const allFacilityBounds = L.featureGroup(allFacilityMarkers).getBounds();
  function recenterMap() {
    if (allFacilityBounds.isValid()) {
      map.flyToBounds(allFacilityBounds, { padding: [36, 36], maxZoom: A.map.zoom, duration: 0.8 });
    } else {
      map.flyTo(center, A.map.zoom);
    }
  }

  if (allFacilityBounds.isValid()) {
    map.fitBounds(allFacilityBounds, { padding: [36, 36], maxZoom: A.map.zoom });
  }
  document.getElementById('btn-recenter').addEventListener('click', recenterMap);

  const facilityCards = [...document.querySelectorAll('#facility-list [data-focus]')];
  const facilityById = new Map(A.facilities.map((facility) => [String(facility.id), facility]));
  const searchInput = document.getElementById('facility-search');
  const emptyMessage = document.getElementById('facility-empty');
  const searchStatus = document.getElementById('facility-search-status');
  const mapNavigationStatus = document.getElementById('map-navigation-status');
  const navigationStatus = document.getElementById('facility-navigation-status');
  const categoryButtons = [...document.querySelectorAll('[data-category-filter]')];
  let selectedCategory = '';

  function applyFacilityFilters() {
    const query = searchInput ? searchInput.value.trim().toLocaleLowerCase('id') : '';
    let visibleCount = 0;

    facilityCards.forEach((card) => {
      const facility = facilityById.get(card.dataset.focus);
      const category = facility && A.categories[facility.category];
      const searchableText = facility
        ? [facility.name, facility.description, facility.address, category && category.label]
            .filter(Boolean)
            .join(' ')
            .toLocaleLowerCase('id')
        : '';
      const matches = searchableText.includes(query) &&
        (!selectedCategory || (facility && facility.category === selectedCategory));

      card.style.display = matches ? '' : 'none';
      const marker = markers[card.dataset.focus];
      if (marker) {
        if (matches && !map.hasLayer(marker)) marker.addTo(map);
        if (!matches && map.hasLayer(marker)) map.removeLayer(marker);
      }
      if (matches) visibleCount++;
    });

    if (emptyMessage) emptyMessage.classList.toggle('hidden', visibleCount > 0);
    if (searchStatus) {
      searchStatus.textContent = query || selectedCategory
        ? `${visibleCount} dari ${facilityCards.length} lokasi ditemukan.`
        : `${facilityCards.length} lokasi ditampilkan.`;
    }
  }

  function activateCategory(category) {
    selectedCategory = category;
    categoryButtons.forEach((button) => {
      const active = button.dataset.categoryFilter === category;
      button.setAttribute('aria-pressed', String(active));
    });

    applyFacilityFilters();
    const categoryMarkers = A.facilities
      .filter((facility) => !category || facility.category === category)
      .map((facility) => markers[facility.id])
      .filter((marker) => marker && map.hasLayer(marker));

    if (categoryMarkers.length === 1) {
      const marker = categoryMarkers[0];
      map.flyTo(marker.getLatLng(), 19, { duration: 0.8 });
      map.once('moveend', () => marker.openPopup());
    } else if (categoryMarkers.length > 1) {
      const bounds = L.featureGroup(categoryMarkers).getBounds();
      if (bounds.isValid()) map.flyToBounds(bounds, { padding: [36, 36], maxZoom: 18, duration: 0.8 });
    } else if (!category) {
      recenterMap();
    }
  }

  categoryButtons.forEach((button) => {
    button.addEventListener('click', () => activateCategory(button.dataset.categoryFilter));
  });

  facilityCards.forEach((card) => {
    card.addEventListener('click', () => {
      const facility = facilityById.get(card.dataset.focus);
      const marker = markers[card.dataset.focus];
      if (!facility || !marker) return;

      if (selectedCategory && facility.category !== selectedCategory) activateCategory('');
      if (searchInput && searchInput.value) {
        searchInput.value = '';
        applyFacilityFilters();
      }

      facilityCards.forEach((item) => {
        item.classList.toggle('is-active', item === card);
      });

      const message = `Menampilkan lokasi: ${facility.name}`;
      mapNavigationStatus.textContent = message;
      mapNavigationStatus.classList.remove('hidden');
      navigationStatus.textContent = message;
      navigationStatus.classList.remove('hidden');

      document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
      window.setTimeout(() => {
        map.invalidateSize();
        map.flyTo(marker.getLatLng(), 19, { duration: 0.8 });
        map.once('moveend', () => marker.openPopup());
      }, 350);
    });
  });

  if (searchInput) searchInput.addEventListener('input', applyFacilityFilters);

  // Mode bantu: /?pick=1 -> klik peta untuk melihat koordinat
  if (new URLSearchParams(location.search).get('pick') === '1') {
    map.on('click', (e) => {
      const t = `${e.latlng.lat.toFixed(6)}, ${e.latlng.lng.toFixed(6)}`;
      L.popup().setLatLng(e.latlng).setContent(`<div class="rounded-xl bg-white p-3 text-xs shadow-lg">Koordinat:<br><b>${t}</b></div>`).openOn(map);
      if (navigator.clipboard) navigator.clipboard.writeText(t).catch(() => {});
    });
  }

  /* ---------- QR CODE ---------- */
  const qrEl = document.getElementById('qrcode');
  new QRCode(qrEl, {
    text: A.baseUrl,
    width: 220,
    height: 220,
    colorDark: '#0f172a',
    colorLight: '#ffffff',
    correctLevel: QRCode.CorrectLevel.H
  });

  // Unduh poster informasi warga dengan tata letak editorial untuk dicetak.
  function loadPosterImage(src) {
    return new Promise((resolve, reject) => {
      const image = new Image();
      image.onload = () => resolve(image);
      image.onerror = () => reject(new Error(`Gagal memuat gambar poster: ${src}`));
      image.src = src;
    });
  }

  function drawPosterLogo(ctx, image, x, y, maxSize) {
    const scale = Math.min(maxSize / image.width, maxSize / image.height);
    const width = image.width * scale;
    const height = image.height * scale;
    ctx.drawImage(image, x + (maxSize - width) / 2, y + (maxSize - height) / 2, width, height);
  }

  document.getElementById('btn-download').addEventListener('click', async () => {
    const src = qrEl.querySelector('canvas');
    if (!src) {
      alert('QR Code belum siap. Silakan coba lagi sebentar.');
      return;
    }

    try {
      await document.fonts.ready;
      const [universityLogo, kknLogo] = await Promise.all([
        loadPosterImage(`${A.baseUrl}assets/logo-universitas-tangerang-raya.png`),
        loadPosterImage(`${A.baseUrl}assets/logo-kkn-kelompok-28.jpeg`)
      ]);
      const W = 1240, H = 1754;
      const font = "'Plus Jakarta Sans', 'Segoe UI', Arial, sans-serif";
      const c = document.createElement('canvas');
      c.width = W;
      c.height = H;
      const x = c.getContext('2d');
      if (!x) throw new Error('Browser tidak dapat membuat poster.');

      x.fillStyle = '#f2f0e8';
      x.fillRect(0, 0, W, H);
      x.fillStyle = '#294c3a';
      x.fillRect(64, 64, W - 128, 450);

      x.fillStyle = '#ffffff';
      x.fillRect(108, 102, 88, 88);
      x.fillRect(210, 102, 88, 88);
      drawPosterLogo(x, universityLogo, 116, 110, 72);
      drawPosterLogo(x, kknLogo, 218, 110, 72);

      x.textAlign = 'left';
      x.fillStyle = '#e8b66d';
      x.fillRect(326, 105, 4, 82);
      x.fillStyle = '#f7f5ef';
      x.font = `700 18px ${font}`;
      x.fillText('UNIVERSITAS TANGERANG RAYA', 354, 139);
      x.fillStyle = '#cad9ce';
      x.font = `500 17px ${font}`;
      x.fillText('KULIAH KERJA NYATA  /  KELOMPOK 28', 354, 171);

      x.strokeStyle = 'rgba(247,245,239,.18)';
      x.lineWidth = 2;
      x.beginPath();
      x.moveTo(108, 224);
      x.lineTo(1132, 224);
      x.stroke();

      x.fillStyle = '#cad9ce';
      x.font = `700 18px ${font}`;
      x.fillText('PETA LINGKUNGAN WARGA', 108, 284);
      x.fillStyle = '#ffffff';
      x.font = `800 67px ${font}`;
      x.fillText('DENAH DIGITAL', 108, 366);
      x.fillStyle = '#e8b66d';
      x.fillRect(110, 394, 74, 7);
      x.fillStyle = '#ffffff';
      x.font = `700 33px ${font}`;
      x.fillText(A.title, 108, 450, 790);
      x.fillStyle = '#cad9ce';
      x.font = `500 21px ${font}`;
      x.fillText(A.region, 108, 486, 790);

      const frameSize = 850, frameX = (W - frameSize) / 2, frameY = 554;
      x.fillStyle = '#ffffff';
      x.fillRect(frameX, frameY, frameSize, frameSize);
      x.strokeStyle = '#d9ded6';
      x.lineWidth = 3;
      x.strokeRect(frameX, frameY, frameSize, frameSize);
      x.imageSmoothingEnabled = false;
      const qrSize = 770;
      x.drawImage(src, (W - qrSize) / 2, frameY + 40, qrSize, qrSize);

      x.textAlign = 'center';
      x.fillStyle = '#26352b';
      x.font = `700 34px ${font}`;
      x.fillText('BUKA DENAH DI PONSEL ANDA', W / 2, 1450);
      x.fillStyle = '#526258';
      x.font = `500 23px ${font}`;
      x.fillText('Arahkan kamera ke kode QR untuk mulai menjelajah', W / 2, 1492);

      x.fillStyle = '#294c3a';
      x.fillRect(64, 1540, W - 128, 150);
      x.fillStyle = '#e8b66d';
      x.fillRect(64, 1540, 9, 150);
      x.textAlign = 'left';
      x.fillStyle = '#cad9ce';
      x.font = `600 16px ${font}`;
      x.fillText('AKSES DENAH DIGITAL', 108, 1584);
      x.fillStyle = '#ffffff';
      x.font = `600 22px ${font}`;
      x.fillText(A.baseUrl, 108, 1624, 1020);
      x.fillStyle = '#cad9ce';
      x.font = `500 17px ${font}`;
      x.fillText(A.credit, 108, 1663, 1020);

      const a = document.createElement('a');
      a.download = 'poster-denah-digital.png';
      a.href = c.toDataURL('image/png');
      a.click();
    } catch (error) {
      console.error('Gagal membuat poster:', error);
      alert('Poster gagal dibuat. Periksa koneksi lalu coba unduh kembali.');
    }
  });

  document.getElementById('btn-print').addEventListener('click', () => window.print());

  const copyBtn = document.getElementById('btn-copy');
  const copyLabel = document.getElementById('btn-copy-label');
  copyBtn.addEventListener('click', async () => {
    try {
      await navigator.clipboard.writeText(A.baseUrl);
      copyLabel.textContent = '✓ Tersalin';
    } catch (e) {
      prompt('Salin link berikut:', A.baseUrl);
    }
    setTimeout(() => (copyLabel.textContent = 'Salin Link'), 2000);
  });
})();
