const { FlightRadar24API } = require("flightradarapi");
const mqtt = require("mqtt");
const fs = require("fs");
const axios = require("axios");
const WebSocket = require("ws");

const frApi = new FlightRadar24API();
const wss = new WebSocket.Server({ port: 8080 });

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = ((lat2 - lat1) * Math.PI) / 180;
    const dLon = ((lon2 - lon1) * Math.PI) / 180;
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos((lat1 * Math.PI) / 180) *
            Math.cos((lat2 * Math.PI) / 180) *
            Math.sin(dLon / 2) *
            Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

async function getFilteredFlights(lat, lon, maxDistance) {
    try {
        const bounds = frApi.getBoundsByPoint(lat, lon, maxDistance * 200);
        const flights = await frApi.getFlights(null, bounds);

        return flights.filter((flight) => {
            const distance = calculateDistance(
                lat,
                lon,
                flight.latitude,
                flight.longitude
            );
            return distance <= maxDistance;
        });
    } catch (error) {
        console.error("Error fetching flights:", error);
        throw error;
    }
}

// 🟩 Transformasi ke struktur per pesawat dengan track
function transformToTrackFormat(flights, timestamp) {
    const trackMap = {};

    flights.forEach((flight) => {
        const key = flight.icao24bit || flight.id;
        if (!trackMap[key]) {
            trackMap[key] = {
                icao24bit: flight.icao24bit,
                callsign: flight.callsign,
                track: [],
            };
        }

        trackMap[key].track.push({
            timestamp,
            latitude: flight.latitude,
            longitude: flight.longitude,
            altitude: flight.altitude,
            speed: flight.groundSpeed,
            heading: flight.heading,
        });
    });

    return Object.values(trackMap);
}

let messageCounter = 0;
let flightCountPer5Min = 0; // 🔵 Tambahan: Counter laporan 5 menit
let lat = -6.914744;
let lon = 107.60981;
const maxDistance = 1000;

const mqttBrokerUrl = "mqtt://8.215.50.229:1883";
const username = "pjs-2024";
const password = "@RtiMan017";
const topic = "flightradar/filteredFlights";
const client = mqtt.connect(mqttBrokerUrl, { username, password });

let flightDataBuffer = [];

async function fetchAndSendFlights(client, topic, lat, lon, maxDistance) {
    try {
        const filteredFlights = await getFilteredFlights(lat, lon, maxDistance);
        messageCounter++;
        flightCountPer5Min += filteredFlights.length; // ✅ Tambahkan ke counter

        const now = new Date();
        const localTimestamp = now.toISOString();
        const message = JSON.stringify(filteredFlights);

        // 🟢 MQTT
        client.publish(topic, message, { qos: 0 }, (error) => {
            if (error) {
                console.error(
                    `#${messageCounter} - ${localTimestamp} - MQTT Error: ${error}`
                );
            }
        });

        // 🔵 WebSocket
        wss.clients.forEach((wsClient) => {
            if (wsClient.readyState === WebSocket.OPEN) {
                wsClient.send(message);
            }
        });

        // ⏺️ Simpan ke buffer
        flightDataBuffer.push({
            timestamp: localTimestamp,
            flights: filteredFlights,
        });

        console.log(
            `#${messageCounter} - ${localTimestamp} - Kirim ${filteredFlights.length} pesawat`
        );
    } catch (error) {
        console.error("fetchAndSendFlights Error:", error);
    }
}

// ⏱️ Setiap 5 menit, simpan file log per track + kirim laporan
setInterval(() => {
    if (flightDataBuffer.length === 0) return;

    const now = new Date();
    const timestamp = now.toISOString();

    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, "0");
    const day = String(now.getDate()).padStart(2, "0");
    const hour = String(now.getHours()).padStart(2, "0");
    const minute = String(now.getMinutes()).padStart(2, "0");
    const second = String(now.getSeconds()).padStart(2, "0");

    const tanggal = now.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });

    const baseId = `${year}${month}${day}-${hour}${minute}${second}`;
    const logFilename = `Database_pesawat/flight_log_${baseId}.json`;

    // Gabungkan track semua pesawat berdasarkan timestamp
    const combined = {};
    flightDataBuffer.forEach((entry) => {
        entry.flights.forEach((flight) => {
            const key = flight.icao24bit || flight.id;
            if (!combined[key]) {
                combined[key] = {
                    icao24bit: flight.icao24bit,
                    callsign: flight.callsign,
                    track: [],
                };
            }
            combined[key].track.push({
                timestamp: entry.timestamp,
                latitude: flight.latitude,
                longitude: flight.longitude,
                altitude: flight.altitude,
                speed: flight.groundSpeed,
                heading: flight.heading,
            });
        });
    });

    const logEntry = {
        id: `log-${baseId}-0001`,
        tanggal,
        timestamp,
        data: Object.values(combined),
    };

    fs.writeFileSync(logFilename, JSON.stringify([logEntry], null, 2));
    console.log(`📁 Log disimpan: ${logFilename}`);

    axios
        .get(`http://127.0.0.1:8000/import-log/${logFilename.split("/").pop()}`)
        .then((res) =>
            console.log("📦 Data dikirim ke Laravel:", res.data.message)
        )
        .catch((err) =>
            console.error("❌ Kirim ke Laravel gagal:", err.message)
        );

    // 🔴 Kirim laporan jumlah pesawat per 5 menit ke WebSocket
    const reportPayload = {
        type: "report_5min",
        count: flightCountPer5Min,
        timestamp: timestamp,
    };

    wss.clients.forEach((wsClient) => {
        if (wsClient.readyState === WebSocket.OPEN) {
            wsClient.send(JSON.stringify(reportPayload));
        }
    });

    console.log(`📊 Jumlah data masuk 5 menit terakhir: ${flightCountPer5Min}`);
    flightCountPer5Min = 0; // Reset counter
    flightDataBuffer = [];
}, 5 * 60 * 1000);

client.on("connect", () => {
    console.log("Connected to MQTT broker");
    client.subscribe("gps/position");

    setInterval(() => {
        fetchAndSendFlights(client, topic, lat, lon, maxDistance);
    }, 2000);

    fetchAndSendFlights(client, topic, lat, lon, maxDistance);
});

client.on("message", (topic, message) => {
    const trimmedData = message.toString().slice(1, -1);
    const [latitude, longitude] = trimmedData.split("#");
    lat = latitude;
    lon = longitude;
});
