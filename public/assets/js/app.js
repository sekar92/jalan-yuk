/**
 * Jalan Yuk! - Core Frontend Script
 * Handling: Maps Extractor, Leaflet Maps, Dynamic Filters, PWA Service Worker
 */

document.addEventListener('DOMContentLoaded', () => {
    initNavbarDropdowns();
    initCoordinateExtractor();
    initDynamicMediaInputs();
    initSingleSpotMap();
    initMasterMap();
    initImagePreview();
    registerServiceWorker();
});

/* -------------------------------------------------------------
 * Navbar Dropdowns (Click Toggle, Keyboard Escape & Outside Click)
 * ------------------------------------------------------------- */
function initNavbarDropdowns() {
    const userBtn = document.getElementById('user-menu-btn');
    const userWrapper = document.getElementById('user-dropdown-wrapper');
    const adminBtn = document.getElementById('admin-menu-btn');
    const adminWrapper = document.getElementById('admin-dropdown-wrapper');

    function toggleMenu(wrapper, btn) {
        if (!wrapper || !btn) return;
        const isOpen = wrapper.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    function closeAllMenus() {
        if (userWrapper) {
            userWrapper.classList.remove('is-open');
            userBtn?.setAttribute('aria-expanded', 'false');
        }
        if (adminWrapper) {
            adminWrapper.classList.remove('is-open');
            adminBtn?.setAttribute('aria-expanded', 'false');
        }
    }

    if (userBtn && userWrapper) {
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (adminWrapper) adminWrapper.classList.remove('is-open');
            toggleMenu(userWrapper, userBtn);
        });
    }

    if (adminBtn && adminWrapper) {
        adminBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (userWrapper) userWrapper.classList.remove('is-open');
            toggleMenu(adminWrapper, adminBtn);
        });
    }

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.user-badge-menu') && !e.target.closest('.nav-dropdown')) {
            closeAllMenus();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAllMenus();
        }
    });
}

/* -------------------------------------------------------------
 * 1. Google Maps URL Coordinate Extractor (AJAX)
 * ------------------------------------------------------------- */
function initCoordinateExtractor() {
    const btn = document.getElementById('btn-extract-coords');
    const urlInput = document.getElementById('maps_url');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const spinner = document.getElementById('extract-spinner');
    const hint = document.getElementById('extract-status-hint');

    if (!btn || !urlInput) return;

    btn.addEventListener('click', async () => {
        const url = urlInput.value.trim();
        if (!url) {
            alert('Silakan masukkan link Google Maps terlebih dahulu.');
            urlInput.focus();
            return;
        }

        btn.disabled = true;
        if (spinner) spinner.classList.remove('icon-spin-hidden');
        if (hint) {
            hint.textContent = 'Mengekstrak koordinat dari URL...';
            hint.style.color = 'var(--primary)';
        }

        try {
            const formData = new FormData();
            formData.append('url', url);

            const res = await fetch('/api/extract-coords', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            if (data.success && data.latitude !== null && data.longitude !== null) {
                if (latInput) latInput.value = data.latitude;
                if (lngInput) lngInput.value = data.longitude;

                if (hint) {
                    hint.textContent = `✓ Koordinat berhasil terdeteksi: ${data.latitude}, ${data.longitude}`;
                    hint.style.color = 'var(--success-dark)';
                }
            } else {
                if (hint) {
                    hint.textContent = data.error || 'Gagal mengekstrak koordinat otomatis. Silakan masukkan manual.';
                    hint.style.color = 'var(--danger)';
                }
            }
        } catch (err) {
            console.error('Error extracting coords:', err);
            if (hint) {
                hint.textContent = 'Terjadi kesalahan jaringan saat mengekstrak koordinat.';
                hint.style.color = 'var(--danger)';
            }
        } finally {
            btn.disabled = false;
            if (spinner) spinner.classList.add('icon-spin-hidden');
        }
    });
}

/* -------------------------------------------------------------
 * 2. Dynamic Media Inputs (Create Form)
 * ------------------------------------------------------------- */
function initDynamicMediaInputs() {
    const btnAdd = document.getElementById('btn-add-media-input');
    const container = document.getElementById('media-inputs-container');

    if (!btnAdd || !container) return;

    btnAdd.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'media-input-row';
        row.style.display = 'flex';
        row.style.gap = '0.5rem';
        row.style.marginTop = '0.5rem';

        row.innerHTML = `
            <input type="url" name="media_urls[]" class="form-control" placeholder="https://www.tiktok.com/@... atau https://youtu.be/... atau Instagram">
            <button type="button" class="btn btn-outline btn-sm btn-remove-media" style="color: var(--danger);">&times;</button>
        `;

        row.querySelector('.btn-remove-media').addEventListener('click', () => {
            row.remove();
        });

        container.appendChild(row);
    });
}

/* -------------------------------------------------------------
 * 3. Single Spot Map (Location Detail Page)
 * ------------------------------------------------------------- */
function configureLeafletIcons() {
    if (typeof L === 'undefined') return;
    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconRetinaUrl: '/assets/leaflet/marker-icon-2x.png',
        iconUrl: '/assets/leaflet/marker-icon.png',
        shadowUrl: '/assets/leaflet/marker-shadow.png',
    });
}

function createReliableTileLayer() {
    // High-performance, clean, unblocked map tiles via Esri World Street Map (No API Key Required)
    return L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 18,
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, METI, TomTom'
    });
}

function initSingleSpotMap() {
    const mapEl = document.getElementById('single-spot-map');
    if (!mapEl || typeof L === 'undefined') return;

    configureLeafletIcons();

    const lat = parseFloat(mapEl.dataset.lat);
    const lng = parseFloat(mapEl.dataset.lng);
    const title = mapEl.dataset.title || 'Lokasi';

    if (isNaN(lat) || isNaN(lng)) return;

    const map = L.map(mapEl, {
        scrollWheelZoom: false
    }).setView([lat, lng], 15);

    createReliableTileLayer().addTo(map);

    const marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup(`<strong>${escapeHtml(title)}</strong>`).openPopup();

    setTimeout(() => { map.invalidateSize(); }, 200);
}

/* -------------------------------------------------------------
 * 4. Master Map View (Leaflet + Interactive Sidebar)
 * ------------------------------------------------------------- */
function initMasterMap() {
    const mapEl = document.getElementById('master-map-container');
    if (!mapEl || typeof L === 'undefined') return;

    configureLeafletIcons();

    const map = L.map(mapEl, {
        zoomControl: true,
        attributionControl: true
    }).setView([-2.5489, 118.0149], 5); // Center on Indonesia

    createReliableTileLayer().addTo(map);

    // Force map to adapt immediately to container height
    setTimeout(() => { map.invalidateSize(); }, 150);
    window.addEventListener('resize', () => { map.invalidateSize(); });

    let markersLayer = L.layerGroup().addTo(map);
    let markerInstances = {};

    const categorySelect = document.getElementById('map-filter-category');
    const statusSelect = document.getElementById('map-filter-status');
    const searchInput = document.getElementById('map-search-query');
    const sidebarList = document.getElementById('map-spots-sidebar-list');
    const countIndicator = document.getElementById('spots-count-indicator');

    async function loadMapData() {
        const params = new URLSearchParams();
        if (categorySelect && categorySelect.value) params.set('category', categorySelect.value);
        if (statusSelect && statusSelect.value) params.set('status', statusSelect.value);
        if (searchInput && searchInput.value.trim()) params.set('q', searchInput.value.trim());

        if (countIndicator) countIndicator.textContent = 'Memuat lokasi...';

        try {
            const res = await fetch(`/api/map-locations?${params.toString()}`);
            const data = await res.json();
            const locations = data.locations || [];

            renderMapMarkers(locations);
            renderSidebarList(locations);
            setTimeout(() => { map.invalidateSize(); }, 200);
        } catch (err) {
            console.error('Failed to load map data:', err);
            if (countIndicator) countIndicator.textContent = 'Gagal memuat lokasi.';
        }
    }

    function renderMapMarkers(locations) {
        markersLayer.clearLayers();
        markerInstances = {};

        if (locations.length === 0) {
            if (countIndicator) countIndicator.textContent = '0 tempat ditemukan di peta';
            return;
        }

        if (countIndicator) countIndicator.textContent = `${locations.length} tempat ditemukan di peta`;

        const bounds = [];

        locations.forEach(loc => {
            const lat = loc.latitude;
            const lng = loc.longitude;
            bounds.push([lat, lng]);

            const marker = L.marker([lat, lng]);

            const statusBadge = loc.visit_status === 'Favorite' ? '⭐ Favorite' : (loc.visit_status === 'Visited' ? '✅ Visited' : '📌 Wishlist');

            const popupContent = `
                <div class="map-popup-card">
                    <span class="badge badge-category" style="margin-bottom: 4px;">${escapeHtml(loc.category_name)}</span>
                    <h4 class="popup-title">${escapeHtml(loc.title)}</h4>
                    <span class="badge badge-status-${loc.visit_status.toLowerCase()}">${statusBadge}</span>
                    ${loc.personal_notes ? `<p class="popup-notes">💡 ${escapeHtml(loc.personal_notes)}</p>` : ''}
                    <div class="popup-actions">
                        <a href="${loc.detail_url}" class="btn btn-primary btn-xs" style="color: #ffffff !important;">Buka Detail</a>
                        <a href="${escapeHtml(loc.maps_url)}" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-xs" style="color: var(--slate-700) !important;">Buka Maps</a>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent);
            marker.on('click', () => {
                setActiveSpot(loc.id);
            });
            markersLayer.addLayer(marker);
            markerInstances[loc.id] = marker;
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
        }
    }

    function setActiveSpot(id) {
        document.querySelectorAll('.spot-sidebar-item').forEach(el => {
            if (el.dataset.id === String(id)) {
                el.classList.add('active');
                el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                el.classList.remove('active');
            }
        });
    }

    function renderSidebarList(locations) {
        if (!sidebarList) return;
        sidebarList.innerHTML = '';

        if (locations.length === 0) {
            sidebarList.innerHTML = '<div style="padding: 1.5rem; color: var(--slate-500); text-align: center;">Tidak ada tempat yang sesuai dengan filter.</div>';
            return;
        }

        locations.forEach(loc => {
            const item = document.createElement('div');
            item.className = 'spot-sidebar-item';
            item.dataset.id = loc.id;

            const statusBadge = loc.visit_status === 'Favorite' ? '⭐' : (loc.visit_status === 'Visited' ? '✅' : '📌');

            item.innerHTML = `
                <div class="spot-item-title">${escapeHtml(loc.title)}</div>
                <div class="spot-item-meta">
                    <span class="badge badge-category">${escapeHtml(loc.category_name)}</span>
                    <span class="badge badge-status-${loc.visit_status.toLowerCase()}">${statusBadge} ${loc.visit_status}</span>
                </div>
            `;

            item.addEventListener('click', () => {
                setActiveSpot(loc.id);

                const marker = markerInstances[loc.id];
                if (marker) {
                    map.setView([loc.latitude, loc.longitude], 15);
                    marker.openPopup();
                }
            });

            sidebarList.appendChild(item);
        });
    }

    // Event listeners
    if (categorySelect) categorySelect.addEventListener('change', loadMapData);
    if (statusSelect) statusSelect.addEventListener('change', loadMapData);

    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(loadMapData, 350);
        });
    }

    // Initial load
    loadMapData();
}

/* -------------------------------------------------------------
 * 5. Image File Preview
 * ------------------------------------------------------------- */
function initImagePreview() {
    const fileInput = document.getElementById('image_file');
    const previewContainer = document.getElementById('image-preview');

    if (!fileInput || !previewContainer) return;

    fileInput.addEventListener('change', () => {
        previewContainer.innerHTML = '';
        const file = fileInput.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '180px';
                img.style.maxHeight = '120px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = 'var(--radius-md)';
                img.style.marginTop = '0.75rem';
                img.style.border = '1px solid var(--slate-200)';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });
}

/* -------------------------------------------------------------
 * 6. Progressive Web App (Service Worker Registration)
 * ------------------------------------------------------------- */
function registerServiceWorker() {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => {
                    console.log('Jalan Yuk! PWA Service Worker registered:', reg.scope);
                })
                .catch(err => {
                    console.warn('PWA Service Worker registration skipped:', err);
                });
        });
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
