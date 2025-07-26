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
let isLocating = false; // status toggle

// Inisialisasi locateControl
const locateControl = L.control
    .locate({
        setView: true,
        keepCurrentZoomLevel: true,
        flyTo: false,
        drawCircle: false,
        showPopup: false,
        markerStyle: { weight: 2, opacity: 1, fillOpacity: 1 },
        circleStyle: { weight: 1, opacity: 0.5, fillOpacity: 0.2 },
        locateOptions: {
            enableHighAccuracy: true,
        },
    })
    .addTo(map);

// Hapus tombol locate default dari DOM
const builtInLocate = document.querySelector(".leaflet-control-locate");
if (builtInLocate) {
    builtInLocate.remove(); // benar-benar hapus dari DOM
}

// Sembunyikan tombol bawaan plugin
document.querySelector(".leaflet-control-locate")?.classList.add("hidden");

const button = document.getElementById("custom-locate-btn");

button.addEventListener("click", function () {
    if (isLocating) {
        locateControl.stop(); // stop tracking
        isLocating = false;
    } else {
        locateControl.start(); // start tracking
        isLocating = true;
    }
    updateButtonState();
});

// Opsional: Update tampilan tombol (misal highlight saat aktif)
function updateButtonState() {
    if (isLocating) {
        button.classList.add("active-locate");
    } else {
        button.classList.remove("active-locate");
    }
}

//draw toolbae
// FeatureGroup tempat menyimpan hasil gambar
const drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);

// Inisialisasi draw tools
const drawControl = {
    polygon: new L.Draw.Polygon(map, { shapeOptions: { color: "red" } }),
    polyline: new L.Draw.Polyline(map, { shapeOptions: { color: "green" } }),
    rectangle: new L.Draw.Rectangle(map, { shapeOptions: { color: "blue" } }),
    circle: new L.Draw.Circle(map, { shapeOptions: { color: "orange" } }),
    marker: new L.Draw.Marker(map, {
        icon: new L.Icon.Default(),
        repeatMode: false,
    }),
};

const editControl = new L.EditToolbar.Edit(map, {
    featureGroup: drawnItems,
    selectedPathOptions: { maintainColor: true, opacity: 0.6 },
});

const deleteControl = new L.EditToolbar.Delete(map, {
    featureGroup: drawnItems,
});

// Utility untuk menonaktifkan semua tools
function disableAllTools() {
    drawControl.polygon.disable();
    drawControl.polyline.disable();
    drawControl.rectangle.disable();
    drawControl.marker.disable();
    editControl.disable();
    deleteControl.disable();
}

// Utility untuk menonaktifkan semua tombol aktif
function deactivateAllButtons() {
    document
        .querySelectorAll(".draw-btn")
        .forEach((btn) => btn.classList.remove("active"));
}

// Utility gabungan
function resetToolsAndButtons() {
    disableAllTools();
    deactivateAllButtons();
}

// Toggle handler
function setupToggleButton(buttonId, tool, isMarker = false) {
    const button = document.getElementById(buttonId);
    button.addEventListener("click", (e) => {
        const isActive = button.classList.contains("active");
        resetToolsAndButtons();

        if (!isActive) {
            if (isMarker) {
                // Delay sedikit agar klik tombol tidak dihitung klik peta
                setTimeout(() => {
                    tool.enable();
                }, 100); // 100ms sudah cukup
            } else {
                tool.enable();
            }
            button.classList.add("active");
        }
    });
}

// Pasang toggle ke semua tombol
setupToggleButton("btn-draw-polygon", drawControl.polygon);
setupToggleButton("btn-draw-polyline", drawControl.polyline);
setupToggleButton("btn-draw-rectangle", drawControl.rectangle);
setupToggleButton("btn-draw-circle", drawControl.circle);
setupToggleButton("btn-draw-marker", drawControl.marker, true);
setupToggleButton("btn-edit", editControl);
setupToggleButton("btn-delete", deleteControl);

// Setelah gambar selesai, nonaktifkan mode
map.on(L.Draw.Event.CREATED, function (e) {
    const layer = e.layer;
    drawnItems.addLayer(layer);

    let popupContent = "";

    // Polyline: hitung total panjang (meter)
    if (layer instanceof L.Polyline && !(layer instanceof L.Polygon)) {
        const latlngs = layer.getLatLngs();
        let totalDistance = 0;
        for (let i = 1; i < latlngs.length; i++) {
            totalDistance += latlngs[i - 1].distanceTo(latlngs[i]);
        }

        const distanceStr =
            totalDistance >= 1000
                ? (totalDistance / 1000).toFixed(2) + " km"
                : totalDistance.toFixed(1) + " m";

        popupContent = `Total length: <strong>${distanceStr}</strong>`;
    }

    // Circle: tampilkan radius
    else if (layer instanceof L.Circle) {
        const radius = layer.getRadius();
        const radiusStr =
            radius >= 1000
                ? (radius / 1000).toFixed(2) + " km"
                : radius.toFixed(1) + " m";

        popupContent = `Radius: <strong>${radiusStr}</strong>`;
    }

    // Polygon (opsional): hitung luas
    else if (layer instanceof L.Polygon) {
        try {
            const geojson = layer.toGeoJSON();
            const area = turf.area(geojson); // pakai turf.js
            const areaStr =
                area >= 1000000
                    ? (area / 1000000).toFixed(2) + " km²"
                    : area.toFixed(0) + " m²";

            popupContent = `Area: <strong>${areaStr}</strong>`;
        } catch (err) {
            popupContent = "Polygon created";
        }
    }

    if (popupContent) {
        layer.bindPopup(popupContent).openPopup();
    }

    resetToolsAndButtons(); // keluar dari mode setelah selesai
});

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

//close pop-up kanan
function toggleRightPopupMenu() {
    const popup = document.getElementById("rightPopupMenu");
    popup.style.display = "none";
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

        // if (saveMode && canSave && flightDataArray.length > 0) {
        //     const firstFlight = flightDataArray[0];
        //     fetch("/api/save-adsb", {
        //         method: "POST",
        //         headers: {
        //             "Content-Type": "application/json",
        //             "X-CSRF-TOKEN": csrfToken,
        //         },
        //         body: JSON.stringify({
        //             callsign: firstFlight.callsign,
        //             lat: firstFlight.latitude,
        //             lon: firstFlight.longitude,
        //             altitude: firstFlight.altitude,
        //             speed: firstFlight.groundSpeed,
        //             heading: firstFlight.heading,
        //         }),
        //     })
        //         .then((res) => res.json())
        //         .then((resp) => {
        //             console.log("✅ Data tersimpan:", resp);
        //             lastSaveTime = now;
        //         })
        //         .catch((error) => {
        //             console.error("Error saat simpan:", error);
        //         });
        // }
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

// --- SIMPAN KE DATABASE LOGIC --- //
let saveMode = false;
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

// document.getElementById("toggleSave").addEventListener("change", function () {
//     saveMode = this.checked;
//     console.log("Simpan ke Database:", saveMode);
// });
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
}

document
    .getElementById("saveFollowedBtn")
    .addEventListener("click", function (event) {
        saveFollowedAircrafts(event.target.checked);
    });

document.getElementById("userModal").style.display = "block";
document.getElementById("userModal").classList.add("show");
document.body.classList.add("modal-open");
window.onload = () => {
    const modal = document.getElementById("userModal");
    modal.style.display = "block";
    modal.classList.add("show");
    document.body.classList.add("modal-open");

    // Buat backdrop manual
    const backdrop = document.createElement("div");
    backdrop.className = "modal-backdrop show";
    document.body.appendChild(backdrop);
};

socket.onopen = () => console.log("WebSocket terhubung");
socket.onerror = (error) => console.error("WebSocket error:", error.message);
socket.onclose = () => console.log("WebSocket ditutup");
