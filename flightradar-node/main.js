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

let messageCounter = 0;
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

        const now = new Date();
        const localTimestamp = now.toLocaleString("sv-SE"); // format: YYYY-MM-DD HH:mm:ss (lokal)

        const message = JSON.stringify(filteredFlights);

        // MQTT
        client.publish(topic, message, { qos: 0 }, (error) => {
            if (error) {
                console.error(
                    `#${messageCounter} - ${localTimestamp} - MQTT Error: ${error}`
                );
            }
        });

        // WebSocket
        wss.clients.forEach((wsClient) => {
            if (wsClient.readyState === WebSocket.OPEN) {
                wsClient.send(message);
            }
        });

        // Simpan sementara ke buffer
        flightDataBuffer.push(...filteredFlights);

        console.log(
            `#${messageCounter} - ${localTimestamp} - Kirim ${filteredFlights.length} pesawat`
        );
    } catch (error) {
        console.error("fetchAndSendFlights Error:", error);
    }
}

// Simpan file setiap 5 menit
setInterval(() => {
    if (flightDataBuffer.length === 0) return;

    const now = new Date();
    const localTimestamp = now.toLocaleString("sv-SE"); // format lokal seperti ISO

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

    const logEntry = {
        id: `log-${baseId}-0001`,
        tanggal,
        timestamp: localTimestamp,
        data: flightDataBuffer,
    };

    fs.writeFileSync(logFilename, JSON.stringify([logEntry], null, 2));
    console.log(
        `📁 Log disimpan: ${logFilename} (${flightDataBuffer.length} entri pesawat)`
    );

    // 🔁 Kirim ke Laravel
    const filename = logFilename.split("/").pop();
    axios
        .get(`http://127.0.0.1:8000/import-log/${filename}`)
        .then((response) => {
            console.log(`📦 Import ke Laravel: ${response.data.message}`);
        })
        .catch((error) => {
            console.error(
                `❌ Gagal import: ${
                    error.response?.data?.error ||
                    error.message ||
                    "Unknown error"
                }`
            );
        });

    flightDataBuffer = [];
}, 5 * 60 * 1000); // 5 menit

client.on("connect", () => {
    console.log("Connected to MQTT broker");
    client.subscribe("gps/position");

    setInterval(() => {
        fetchAndSendFlights(client, topic, lat, lon, maxDistance);
    }, 2000);

    fetchAndSendFlights(client, topic, lat, lon, maxDistance);
});

client.on("message", (topic, message) => {
    const mqttData = message.toString();
    const trimmedData = mqttData.slice(1, -1);
    const [latitude, longitude] = trimmedData.split("#");
    lat = latitude;
    lon = longitude;
});

client.on("error", (error) => console.error("MQTT error:", error));
client.on("offline", () => console.warn("MQTT offline"));
client.on("reconnect", () => console.log("MQTT reconnecting"));
client.on("close", () => console.log("MQTT connection closed"));
