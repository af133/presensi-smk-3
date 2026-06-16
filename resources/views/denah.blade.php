<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denah Satelit SMKN 3 Jember</title>
    <link rel="icon" type="image/png" href="{{ asset('build/assets/icon/smk3.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        body, html { margin: 0; padding: 0; height: 100%; width: 100%; }
        #map { height: 100vh; width: 100vw; }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="overflow-hidden">

    <div class="absolute top-4 left-4 right-4 md:left-6 md:w-96 z-[1000] glass-card p-5 rounded-2xl shadow-2xl">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-lg md:text-xl font-bold text-slate-800">SMKN 3 Jember</h1>
                <p class="text-xs md:text-sm text-slate-600 mt-1">Peta interaktif area sekolah.</p>
            </div>
            <img src="{{ asset('build/assets/icon/smk3.png') }}" alt="Logo" class="w-12 h-12 object-contain">
        </div>
    </div>

    <div id="login-panel" class="fixed z-[1000] 
        top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 
        md:top-6 md:right-6 md:left-auto md:translate-x-0 md:translate-y-0 
        glass-card p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] 
        border border-white/40 bg-white/60 backdrop-blur-2xl
        flex flex-col gap-3 min-w-[280px] w-[90%] md:w-auto transition-opacity duration-300">
        
        <h2 class="text-slate-800 font-bold text-center mb-2">Akses Sistem</h2>
        
        <a href="/login/admin" class="group flex items-center justify-between px-5 py-3 rounded-2xl bg-white/50 hover:bg-red-500 hover:text-white transition-all duration-300 border border-white/50">
            <span class="font-semibold text-slate-700 group-hover:text-white">Login Admin</span>
            <span class="text-xl">➔</span>
        </a>
        
        <a href="/login/guru" class="group flex items-center justify-between px-5 py-3 rounded-2xl bg-white/50 hover:bg-blue-500 hover:text-white transition-all duration-300 border border-white/50">
            <span class="font-semibold text-slate-700 group-hover:text-white">Login Guru</span>
            <span class="text-xl">➔</span>
        </a>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map = L.map('map', { zoomControl: false, maxZoom: 22 }).setView([-8.1516, 113.7020], 19); 
        L.control.zoom({ position: 'bottomleft' }).addTo(map);
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri',
            maxZoom: 22
        }).addTo(map);

        // Logic untuk menyembunyikan panel login saat popup dibuka
        const loginPanel = document.getElementById('login-panel');
        map.on('popupopen', () => loginPanel.style.opacity = '0');
        map.on('popupclose', () => loginPanel.style.opacity = '1');

        fetch("{{ asset('build/assets/maps/denah_smk3_dummy.geojson') }}")
            .then(response => response.json())
            .then(data => {
                var layerGeo = L.geoJSON(data, {
                    style: { color: "#fbbf24", fillColor: "#f59e0b", fillOpacity: 0.5, weight: 3 },
                    onEachFeature: function (feature, layer) {
                        layer.bindPopup(`
                            <div class="p-2">
                                <h3 class="font-bold text-gray-800">${feature.properties.nama || '-'}</h3>
                                <p class="text-xs text-gray-600">Kode: ${feature.properties.kode_ruang || '-'}</p>
                            </div>
                        `);
                    }
                }).addTo(map);

                map.fitBounds(layerGeo.getBounds(), { padding: [50, 50] });
            })
            .catch(err => console.error("Gagal memuat GeoJSON:", err));
    </script>
</body>
</html>