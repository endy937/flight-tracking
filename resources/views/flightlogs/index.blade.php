<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Replay Penerbangan</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        #map {
            width: 100%;
            height: 100vh;
        }

        .controls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: transparent;
            /* 💡 transparan */
            padding: 10px 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 999;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(6px);
            border:
                solid 1px;
            border-color: gray;
            color: #ddd;
            /* 💡 efek kaca buram */
        }


        .map-mode {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
            z-index: 1000;
            background: transparent;
            /* transparan */
            border-radius: 2px;
            padding: 10px;

            align-items: flex-start;


        }

        #mapModeBtn,
        {
        padding: 6px 12px;
        cursor: pointer;
        border: none;
        border-radius: 2px;
        font-weight: bold;
        width: auto;
        height: 30px;
        }

        #mapModeBtn {
            background-color: transparent;
            color: #ddd;
            border: solid 1px;
            border-radius: 2px;
            border-color: gray;
            height: 30px;
            padding: 6px 12px;
            cursor: pointer;
        }

        #mapModeBtn:hover {
            background-color: #83838360;
        }

        #backBtn {
            padding: 6px 15px;
            cursor: pointer;
            border-radius: 2px;
            height: 16px;
            color: #ddd;
            border: solid 1px;
            border-radius: 2px;
            border-color: gray;
            text-decoration: none;

        }

        #backBtn:hover {
            background-color: #83838360;
        }

        .map-mode ul {
            display: none;
            list-style: none;
            padding: 0;
            margin-top: 5px;
            background: transparent;
            border: 1px solid gray;
            border-radius: 2px;
            position: absolute;
            right: 0;
            top: 100%;
            width: 150px;
            z-index: 1001;
            color: #ddd
        }

        .map-mode ul li {
            padding: 8px 12px;
            cursor: pointer;
        }

        .map-mode ul li:hover {
            background-color: #83838360;
        }

        input[type="range"] {
            width: 200px;
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <div class="map-mode">
        <button id="mapModeBtn">Map Mode</button>
        <ul id="mapModeMenu">
            <li class="mode-option" data-mode="streets">Streets</li>
            <li class="mode-option" data-mode="satellite">Satellite</li>
            <li class="mode-option" data-mode="dark">Dark</li>
            <li class="mode-option" data-mode="dark2">Dark 2</li>
            <li class="mode-option" data-mode="topographic">Topographic</li>
            <li class="mode-option" data-mode="hybrid">Hybrid</li>
        </ul>
        <a href="{{ route('logsave_index') }}" id="backBtn">Kembali</a>

    </div>

    <div class="controls">
        <button id="playBtn">Play</button>
        <button id="pauseBtn">Pause</button>
        <input type="range" id="timeSlider" min="0" value="0" />
        <span id="timeLabel"></span>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://rawcdn.githack.com/bbecquet/Leaflet.RotatedMarker/0.2.0/leaflet.rotatedMarker.js"></script>

    <script>
        const logData = JSON.parse(@json($log->data));
        const map = L.map('map', {
            zoomControl: false
        }).setView([-6.1751, 106.865], 9);
        const baseLayers = {
            streets: L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"),
            satellite: L.tileLayer(
                "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}"),
            dark: L.tileLayer("https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png"),
            dark2: L.tileLayer("https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png"),
            topographic: L.tileLayer("https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png"),
            hybrid: L.tileLayer("https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}")
        };

        let currentBaseLayer = baseLayers.dark2;
        currentBaseLayer.addTo(map);

        document.getElementById("mapModeBtn").addEventListener("click", () => {
            const menu = document.getElementById("mapModeMenu");
            menu.style.display = menu.style.display === "none" ? "block" : "none";
        });

        document.querySelectorAll("#mapModeMenu .mode-option").forEach((option) => {
            option.addEventListener("click", (e) => {
                const mode = e.target.getAttribute("data-mode");
                if (currentBaseLayer) map.removeLayer(currentBaseLayer);
                currentBaseLayer = baseLayers[mode];
                currentBaseLayer.addTo(map);
                document.getElementById("mapModeMenu").style.display = "none";
            });
        });

        document.getElementById("backBtn").addEventListener("click", () => {
            window.history.back(); // Bisa diganti ke window.location.href = "/dashboard";
        });

        const planeIcon = L.icon({
            iconUrl: '/home/assets/images/plane.png',
            iconSize: [32, 32],
            iconAnchor: [16, 16],
        });

        const markers = {};
        const timestamps = new Set();

        logData.forEach(flight => {
            if (flight.track) {
                flight.track.forEach(pos => timestamps.add(pos.timestamp));
            }
        });

        const sortedTimestamps = Array.from(timestamps).sort();
        const slider = document.getElementById('timeSlider');
        const timeLabel = document.getElementById('timeLabel');
        slider.max = sortedTimestamps.length - 1;

        let currentIndex = 0;
        let playing = false;
        let interval;

        function updateMap(index) {
            const currentTime = sortedTimestamps[index];
            timeLabel.textContent = currentTime;

            logData.forEach(flight => {
                const pos = flight.track.find(p => p.timestamp === currentTime);
                if (pos && pos.latitude && pos.longitude) {
                    const latlng = [pos.latitude, pos.longitude];
                    const heading = pos.heading || 0;

                    if (!markers[flight.icao24bit]) {
                        const marker = L.marker(latlng, {
                            icon: planeIcon,
                            rotationAngle: heading,
                            rotationOrigin: 'center center'
                        }).addTo(map);
                        marker.bindPopup(`<b>${flight.callsign || 'No Callsign'}</b>`);
                        markers[flight.icao24bit] = marker;
                    } else {
                        markers[flight.icao24bit]
                            .setLatLng(latlng)
                            .setRotationAngle(heading);
                    }
                }
            });
        }

        function play() {
            if (playing) return;
            playing = true;
            interval = setInterval(() => {
                if (currentIndex >= sortedTimestamps.length) {
                    pause();
                    return;
                }
                slider.value = currentIndex;
                updateMap(currentIndex);
                currentIndex++;
            }, 1000);
        }

        function pause() {
            playing = false;
            clearInterval(interval);
        }

        document.getElementById('playBtn').addEventListener('click', play);
        document.getElementById('pauseBtn').addEventListener('click', pause);
        slider.addEventListener('input', (e) => {
            currentIndex = parseInt(e.target.value);
            updateMap(currentIndex);
        });

        updateMap(0);
    </script>
</body>

</html>
