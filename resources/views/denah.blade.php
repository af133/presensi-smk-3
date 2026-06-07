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
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="overflow-hidden">

    <div class="absolute top-6 left-6 z-[1000] glass-card p-6 rounded-2xl shadow-2xl w-96">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">SMKN 3 Jember</h1>
                <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                    Peta interaktif area sekolah. Gunakan tombol + / - di kiri bawah untuk zoom.
                </p>
            </div>
            <img src="{{ asset('build/assets/icon/smk3.png') }}" alt="Logo SMKN 3" class="w-16 h-16 object-contain flex-shrink-0">
        </div>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        
        var map = L.map('map', { 
            zoomControl: false, 
            maxZoom: 22 
        }).setView([-8.1516, 113.7020], 19); 

        L.control.zoom({ position: 'bottomleft' }).addTo(map);
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri',
            maxZoom: 22
        }).addTo(map);

        var resetZoom = L.control({ position: 'topright' });
        resetZoom.onAdd = function (map) {
            var div = L.DomUtil.create('div', 'leaflet-bar');
            div.innerHTML = '<button title="Reset Zoom" style="background:white; width:30px; height:30px; cursor:pointer; display:flex; align-items:center; justify-content:center;">🏠</button>';
            div.onclick = function(){ map.setView([-8.1516, 113.7020], 19); }
            return div;
        };
        resetZoom.addTo(map);
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