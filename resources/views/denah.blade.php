<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Denah Satelit SMKN 3 Jember</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        body, html { margin: 0; padding: 0; height: 100%; width: 100%; }
        #map { height: 100vh; width: 100vw; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .leaflet-popup-content-wrapper { padding: 0 !important; border-radius: 12px !important; overflow: hidden; }
        .leaflet-popup-content { margin: 0 !important; width: auto !important; min-width: 200px; }
        .leaflet-popup-tip { background: #1e293b; }
        
        #loader { transition: opacity 0.5s ease; }
    </style>
</head>
<body class="overflow-hidden">

    <div id="loader" class="fixed inset-0 flex items-center justify-center bg-slate-50 z-[2000]">
        <div class="animate-bounce font-bold text-slate-600">Memuat Peta...</div>
    </div>

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
        glass-card p-6 rounded-3xl shadow-2xl border border-white/40 
        bg-white/70 bg-white/10 backdrop-blur-lg flex flex-col gap-3 min-w-[300px] w-[90%] md:w-96 
        transition-opacity duration-300">
        
        <h2 class="text-slate-800 font-bold text-center mb-2">Akses Sistem</h2>
        
        <a href="/admin/login" class="group flex items-center justify-between px-5 py-3 rounded-2xl bg-white/50 hover:bg-red-500 hover:text-white transition-all duration-300 border border-white/50">
            <span class="font-semibold text-slate-700 group-hover:text-white">Login Admin</span>
            <span>➔</span>
        </a>
        
        <a href="/login" class="group flex items-center justify-between px-5 py-3 rounded-2xl bg-white/50 hover:bg-blue-500 hover:text-white transition-all duration-300 border border-white/50">
            <span class="font-semibold text-slate-700 group-hover:text-white">Login Guru</span>
            <span>➔</span>
        </a>

        <button onclick="document.getElementById('login-panel').style.display='none'" class="text-xs text-slate-400 mt-2 hover:text-slate-600">
            Tutup
        </button>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const rooms = @json($maps);
        var map = L.map('map', { zoomControl: false, maxZoom: 22 }).setView([-8.1516, 113.7020], 19);
        L.control.zoom({ position: 'bottomleft' }).addTo(map);
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri',
            maxZoom: 22,   
            maxNativeZoom: 19,
            zoomOffset: 0
        }).addTo(map);

        const loginPanel = document.getElementById('login-panel');
        map.on('popupopen', () => loginPanel.style.opacity = '0');
        map.on('popupclose', () => loginPanel.style.opacity = '1');

        const roomLayer = L.featureGroup().addTo(map);

        rooms.forEach(room => {
            if (!room.geojson) return;
            try {
                const layer = L.geoJSON(JSON.parse(room.geojson), {
                    style: { color: "#fbbf24", fillColor: "#f59e0b", fillOpacity: 0.5, weight: 3 }
                });

                layer.bindPopup(`
                    <div class="min-w-[180px]">
                        <div class="bg-slate-800 text-white px-3 py-2 rounded-t-lg"><h3 class="font-bold text-sm">${room.name}</h3></div>
                        <div class="p-3 space-y-2">
                            <div class="text-xs text-gray-600">ID: <span class="bg-slate-100 px-1 rounded">${room.room_code}</span></div>
                            <div class="text-xs text-gray-600">Lantai: <span class="bg-amber-500 text-white px-2 rounded-full font-bold">${room.floor}</span></div>
                        </div>
                    </div>
                `, { className: 'custom-popup' });

                layer.addTo(roomLayer);
            } catch (error) { console.error('Error:', error); }
        });

        if (roomLayer.getLayers().length > 0) {
            map.fitBounds(roomLayer.getBounds(), { padding: [50, 50] });
        }
        window.addEventListener('load', () => {
            document.getElementById('loader').style.opacity = '0';
            setTimeout(() => document.getElementById('loader').remove(), 500);
        });
    </script>
</body>
</html>