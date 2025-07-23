// Inisialisasi peta
const map = L.map("map", {
    zoomControl: false,
}).setView([-6.1751, 106.865], 9);

const baseLayers = {
    streets: L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"),
    satellite: L.tileLayer(
        "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}"
    ),
    dark: L.tileLayer(
        "https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png",
        { attribution: "&copy; OpenStreetMap & Stadia Maps" }
    ),
    dark2: L.tileLayer(
        "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png"
    ),
    topographic: L.tileLayer(
        "https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png"
    ),
    hybrid: L.tileLayer("https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}"),
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

L.control.scale().addTo(map);

// Fullscreen button
const fullscreenBtn = document.getElementById("fullscreenBtn");
const fullscreenIcon = document.getElementById("fullscreenIcon");
const containerElement = document.getElementById("mapContainer");

fullscreenBtn.addEventListener("click", () => {
    if (
        !document.fullscreenElement &&
        !document.webkitFullscreenElement &&
        !document.msFullscreenElement
    ) {
        if (containerElement.requestFullscreen)
            containerElement.requestFullscreen();
        else if (containerElement.webkitRequestFullscreen)
            containerElement.webkitRequestFullscreen();
        else if (containerElement.msRequestFullscreen)
            containerElement.msRequestFullscreen();
    } else {
        if (document.exitFullscreen) document.exitFullscreen();
        else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
        else if (document.msExitFullscreen) document.msExitFullscreen();
    }
});

document.addEventListener("fullscreenchange", () => {
    fullscreenIcon.src = document.fullscreenElement
        ? "{{ asset('home/assets/images/exit.png') }}"
        : "{{ asset('home/assets/images/full.png') }}";
});
document.addEventListener("webkitfullscreenchange", () => {
    fullscreenIcon.src = document.webkitFullscreenElement
        ? "{{ asset('home/assets/images/exit.png') }}"
        : "{{ asset('home/assets/images/full.png') }}";
});
document.addEventListener("msfullscreenchange", () => {
    fullscreenIcon.src = document.msFullscreenElement
        ? "{{ asset('home/assets/images/exit.png') }}"
        : "{{ asset('home/assets/images/full.png') }}";
});

// Search
document.getElementById("search-form").addEventListener("submit", function (e) {
    e.preventDefault();
    const query = document.getElementById("search-input").value;
    if (!query) return;
    fetch(
        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
            query
        )}`
    )
        .then((response) => response.json())
        .then((data) => {
            if (data && data.length > 0) {
                map.setView([data[0].lat, data[0].lon], 14);
            } else alert("Lokasi tidak ditemukan");
        })
        .catch((error) => console.error("Error:", error));
});

// Geolocation
L.control
    .locate({
        setView: true,
        keepCurrentZoomLevel: true,
        flyTo: false,
        drawCircle: false,
        markerStyle: { weight: 2, opacity: 1, fillOpacity: 1 },
        circleStyle: { weight: 1, opacity: 0.5, fillOpacity: 0.2 },
    })
    .addTo(map);

// Feature group & draw
const drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);
const drawControl = new L.Control.Draw({
    edit: { featureGroup: drawnItems },
    draw: { polygon: true, polyline: true, rectangle: true, marker: true },
});
map.addControl(drawControl);

map.on(L.Draw.Event.CREATED, function (e) {
    const layer = e.layer;
    drawnItems.addLayer(layer);
    layer.on("click", function () {
        drawnItems.eachLayer(function (l) {
            l.setStyle && l.setStyle({ color: "#3388ff" });
        });
        if (layer.setStyle) layer.setStyle({ color: "yellow" });
        const shapeGeoJSON = layer.toGeoJSON();
        console.log("Shape selected:", shapeGeoJSON);
        layer.bindPopup("Shape Selected!").openPopup();
    });
});
map.on(L.Draw.Event.EDITED, function (e) {});
map.on(L.Draw.Event.DELETED, function (e) {});

// Download
L.Control.Download = L.Control.extend({
    onAdd: function () {
        const container = L.DomUtil.create(
            "div",
            "leaflet-bar leaflet-control leaflet-control-custom"
        );
        const button = L.DomUtil.create("a", "", container);
        button.innerHTML = "💾";
        button.href = "#";
        button.title = "Download GeoJSON";
        button.style.fontSize = "20px";
        button.style.textAlign = "center";
        button.style.lineHeight = "30px";
        L.DomEvent.on(button, "click", function (e) {
            L.DomEvent.stop(e);
            const data = drawnItems.toGeoJSON();
            const blob = new Blob([JSON.stringify(data, null, 2)], {
                type: "application/json",
            });
            const url = URL.createObjectURL(blob);
            const tempLink = document.createElement("a");
            tempLink.href = url;
            tempLink.download = "drawn_shapes.geojson";
            document.body.appendChild(tempLink);
            tempLink.click();
            document.body.removeChild(tempLink);
            URL.revokeObjectURL(url);
        });
        return container;
    },
    onRemove: function () {},
});
L.control.download = function (opts) {
    return new L.Control.Download(opts);
};
L.control.download({ position: "topleft" }).addTo(map);

// Brightness
// document.addEventListener("DOMContentLoaded", function () {
//     const toggleBtn = document.getElementById("toggle-brightness");
//     const sliderWrapper = document.getElementById("slider-wrapper");
//     const slider = document.getElementById("brightness-slider");

//     toggleBtn.addEventListener("click", () => {
//         sliderWrapper.style.display =
//             sliderWrapper.style.display === "none" ? "block" : "none";
//     });

//     slider.addEventListener("input", (e) => {
//         const value = e.target.value;
//         document.querySelectorAll(".leaflet-tile").forEach((tile) => {
//             tile.style.filter = `brightness(${value}%)`;
//         });
//     });
// });

// Map mode
function setMapMode(mode) {
    if (currentBaseLayer) map.removeLayer(currentBaseLayer);
    if (baseLayers[mode]) {
        currentBaseLayer = baseLayers[mode];
        currentBaseLayer.addTo(map);
    }
}

// Popups
function closeAllPopups() {
    document
        .querySelectorAll(".left-popup-menu")
        .forEach((p) => p.classList.remove("active"));
    if (activeTrailId && trackLines[activeTrailId]) {
        map.removeLayer(trackLines[activeTrailId]);
        trackPoints[activeTrailId] = [];
        trackLines[activeTrailId] = null;
        activeTrailId = null;
        selectedAircraftId = null;
    }
}
function togglePopup(id) {
    const popup = document.getElementById(id);
    const isActive = popup.classList.contains("active");
    closeAllPopups();
    if (!isActive) popup.classList.add("active");
}
const togglePopupMenu = () => togglePopup("popupMenu");
const toggleLeftPopupMenuP = () => togglePopup("leftPopupMenuP");

// Koordinat;
// map.on("mousemove", function (e) {
//     document.getElementById("coordinate").innerHTML =
//         e.latlng.lat.toFixed(6) + ", " + e.latlng.lng.toFixed(6);
// });

// --- SIMPAN KE DATABASE LOGIC --- //
let saveMode = false;
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

document.getElementById("toggleSave").addEventListener("change", function () {
    saveMode = this.checked;
    console.log("Simpan ke Database:", saveMode);
});

const socket = new WebSocket("ws://127.0.0.1:8080");
const markers = {};
const trackPoints = {};
const trackLines = {};
let activeTrailId = null;
let selectedAircraftId = null;
let lastSelectedData = null;

function updateMarker(flightData) {
    const { latitude, longitude, id, heading, callsign } = flightData;
    if (!callsign) return;

    if (!isFinite(latitude) || !isFinite(longitude)) {
        console.error("Koordinat tidak valid:", latitude, longitude);
        return;
    }

    const endPos = L.latLng(latitude, longitude);
    const duration = 2000;
    const iconHTML = `
    <div style="transform: rotate(${heading}deg); width: 25px; height: 30px;">
    <img src="home/assets/images/plane.png" style="width: 100%; height: 100%;" alt="plane" />
    </div>`;

    const icon = L.divIcon({
        className: "flight-icon",
        html: iconHTML,
        iconSize: [25, 30], // ukuran sesuai gambar
        iconAnchor: [12.5, 15], // setengah dari width dan height
    });

    if (!markers[callsign]) {
        markers[callsign] = L.marker([latitude, longitude], {
            icon,
            registration: flightData.registration,
            icao24bit: flightData.icao24bit,
        }).addTo(map);
        markers[callsign].on("click", () =>
            showFlightDetails(flightData, callsign)
        );
        trackPoints[callsign] = [];
        trackLines[callsign] = null;
    } else {
        const startPos = markers[callsign].getLatLng();
        interpolateMarker(markers[callsign], startPos, endPos, duration, () => {
            markers[callsign].setIcon(icon);

            // Jika pesawat di-follow, tambahkan point trail
            const isFollowed = followedAircrafts.find(
                (item) => item.id === callsign
            );
            if (isFollowed) {
                trackPoints[callsign].push([latitude, longitude]);
                if (!trackLines[callsign]) {
                    trackLines[callsign] = L.polyline(trackPoints[callsign], {
                        color: "yellow",
                        weight: 2,
                    }).addTo(map);
                } else {
                    trackLines[callsign].setLatLngs(trackPoints[callsign]);
                }
            }
        });
    }

    if (selectedAircraftId === callsign) updatePopupContent(flightData);
}

function updatePopupContent(flightData) {
    const {
        latitude,
        longitude,
        icao24bit,
        heading,
        altitude,
        groundSpeed,
        aircraftCode,
        registration,
        time,
        originAirportIata,
        destinationAirportIata,
        number,
        airlineIata,
        onGround,
        verticalSpeed,
        callsign,
        airlineIcao,
    } = flightData;
    const timeString = new Date(time * 1000).toLocaleString();
    const popupContentP = `
        <strong>Callsign:</strong> ${callsign}<br>
        <strong>Airline ICAO:</strong> ${airlineIcao}<br>
        <strong>Flight Number:</strong> ${number}<br>
        <strong>Aircraft Code:</strong> ${aircraftCode}<br>
        <strong>Registration:</strong> ${registration}<br>
        <strong>Altitude:</strong> ${altitude} ft<br>
        <strong>Speed:</strong> ${groundSpeed} knots<br>
        <strong>Vertical Speed:</strong> ${verticalSpeed} ft/min<br>
        <strong>Heading:</strong> ${heading}°<br>
        <strong>Status:</strong> ${onGround ? "On Ground" : "In Flight"}<br>
        <strong>Origin Airport:</strong> ${originAirportIata}<br>
        <strong>Destination Airport:</strong> ${destinationAirportIata}<br>
        <strong>Latitude:</strong> ${latitude}<br>
        <strong>Longitude:</strong> ${longitude}<br>
        <strong>ICAO 24-bit:</strong> ${icao24bit}<br>
        <strong>Timestamp:</strong> ${timeString}
    `;
    document.getElementById("popupContentP").innerHTML = popupContentP;
}

function showFlightDetails(flightData, callsign) {
    selectedAircraftId = callsign;
    lastSelectedData = flightData; // ✅ simpan data terakhir

    if (!trackPoints[callsign]) {
        trackPoints[callsign] = [markers[callsign].getLatLng()];
    }
    if (!trackLines[callsign]) {
        trackLines[callsign] = L.polyline(trackPoints[callsign], {
            color: "cyan",
            weight: 2,
        }).addTo(map);
    }

    updatePopupContent(flightData);
    document.getElementById("leftPopupMenuP").classList.add("active");
}

function interpolateMarker(marker, startPos, endPos, duration, updateIcon) {
    const startTime = performance.now();
    function animate(time) {
        const elapsedTime = time - startTime;
        const t = Math.min(elapsedTime / duration, 1);
        const lat = startPos.lat + (endPos.lat - startPos.lat) * t;
        const lng = startPos.lng + (endPos.lng - startPos.lng) * t;
        marker.setLatLng([lat, lng]);
        if (t < 1) requestAnimationFrame(animate);
        else if (updateIcon) updateIcon();
    }
    requestAnimationFrame(animate);
}

//simpan ke database
let lastSaveTime = 0;

socket.onmessage = (event) => {
    try {
        const flightDataArray = JSON.parse(event.data);
        console.log("Data diterima:", flightDataArray);

        const now = Date.now();
        const canSave = now - lastSaveTime > 2000;

        flightDataArray.forEach((flightData) => {
            updateMarker(flightData);

            // Update data koordinat real-time di list follow
            const followed = followedAircrafts.find(
                (item) => item.id === flightData.callsign
            );
            if (followed) {
                followed.lat = flightData.latitude.toFixed(6);
                followed.lng = flightData.longitude.toFixed(6);
            }
        });

        // Update list follow biar data koordinat real-time
        updateFollowedList();

        if (saveMode && canSave && flightDataArray.length > 0) {
            const firstFlight = flightDataArray[0];
            fetch("/api/save-adsb", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({
                    callsign: firstFlight.callsign,
                    lat: firstFlight.latitude,
                    lon: firstFlight.longitude,
                    altitude: firstFlight.altitude,
                    speed: firstFlight.groundSpeed,
                    heading: firstFlight.heading,
                }),
            })
                .then((res) => res.json())
                .then((resp) => {
                    console.log("✅ Data tersimpan:", resp);
                    lastSaveTime = now;
                })
                .catch((error) => {
                    console.error("Error saat simpan:", error);
                });
        }
    } catch (error) {
        console.error("Kesalahan parsing:", error.message);
    }
};

//follow
const followedAircrafts = [];
const latestFlightData = {};

function followAircraft() {
    if (!selectedAircraftId) {
        alert("Tidak ada pesawat yang dipilih");
        return;
    }

    const marker = markers[selectedAircraftId];
    if (!marker) {
        alert("Marker tidak ditemukan atau belum dimuat");
        console.error("Marker undefined untuk:", selectedAircraftId);
        return;
    }

    const latlng = marker.getLatLng();
    const registration = marker.options.registration || "-";
    const icao24bit = marker.options.icao24bit || "-";

    if (!followedAircrafts.find((item) => item.id === selectedAircraftId)) {
        followedAircrafts.push({
            id: selectedAircraftId,
            lat: latlng.lat.toFixed(6),
            lng: latlng.lng.toFixed(6),
            registration,
            icao24bit,
        });
        console.log(
            "✅ Pesawat ditambahkan ke daftar follow:",
            selectedAircraftId
        );
    } else {
        console.log("Pesawat sudah di-follow:", selectedAircraftId);
    }

    updateFollowedList();
    document.getElementById("rightPopupMenu").style.display = "block";
}

function updateFollowedList() {
    const container = document.getElementById("popupContentFollow");
    container.innerHTML = "";

    const template = document.getElementById("followTemplate");

    followedAircrafts.forEach((aircraft) => {
        // Clone template
        const clone = template.content.cloneNode(true);

        // Isi data
        clone.querySelector(".follow-callsign").textContent = aircraft.id;
        clone.querySelector(".follow-lat").textContent = aircraft.lat;
        clone.querySelector(".follow-lng").textContent = aircraft.lng;
        clone.querySelector(".follow-registration").textContent =
            aircraft.registration || "-";
        clone.querySelector(".follow-icao").textContent =
            aircraft.icao24bit || "-";

        // Tambahkan ke container
        container.appendChild(clone);

        // Tambahkan garis trail kalau belum
        if (!trackLines[aircraft.id]) {
            trackPoints[aircraft.id] = [markers[aircraft.id].getLatLng()];
            trackLines[aircraft.id] = L.polyline(trackPoints[aircraft.id], {
                color: "yellow",
                weight: 2,
            }).addTo(map);
        }
    });
}

//close pop-up kanan
function toggleRightPopupMenu() {
    const popup = document.getElementById("rightPopupMenu");
    popup.style.display = "none";
}

//reset follow
function resetFollowedAircrafts() {
    // Hapus semua garis trail dari map
    followedAircrafts.forEach((aircraft) => {
        if (trackLines[aircraft.id]) {
            map.removeLayer(trackLines[aircraft.id]);
            trackLines[aircraft.id] = null;
            trackPoints[aircraft.id] = [];
        }
    });

    // Kosongkan array
    followedAircrafts.length = 0;

    // Update tampilan popup
    updateFollowedList();

    console.log("Daftar pesawat di-follow sudah di-reset & garis dihapus.");
}

//save follow
function saveFollowedAircrafts() {
    if (followedAircrafts.length === 0) {
        alert("Belum ada pesawat yang di-follow");
        return;
    }

    followedAircrafts.forEach((aircraft) => {
        const body = JSON.stringify({
            callsign: aircraft.id,
            lat: aircraft.lat,
            lon: aircraft.lng,
            registration: aircraft.registration,
            icao24bit: aircraft.icao24bit,
        });

        console.log("Request body:", body);

        fetch("/follow-aircraft", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: body,
        })
            .then((res) => res.json())
            .then((data) => {
                console.log("✅ Pesawat berhasil disimpan ke DB:", data);
            })
            .catch((error) => {
                console.error("❌ Gagal simpan ke DB:", error);
            });
    });

    alert("Semua pesawat yang di-follow sudah dikirim ke database!");
}

document
    .getElementById("saveFollowedBtn")
    .addEventListener("click", saveFollowedAircrafts);

socket.onopen = () => console.log("WebSocket terhubung");
socket.onerror = (error) => console.error("WebSocket error:", error.message);
socket.onclose = () => console.log("WebSocket ditutup");
