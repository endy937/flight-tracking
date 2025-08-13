const ws = new WebSocket("ws://localhost:8080");

// Global arrays
const labels = [];
const countData = [];
const altitudeData = [];
const speedData = [];
const speedMaxData = [];
const speedMinData = [];

const headingGroups = {
    East: 0,
    South: 0,
    West: 0,
    North: 0,
};
const altitudeBins = {
    "0-5000": 0,
    "5000-10000": 0,
    "10000-15000": 0,
    "15000-20000": 0,
    "20000+": 0,
};

let latestFlightCount = 0;
let lastUpdated = 0; // Menyimpan waktu pembaruan terakhir tabel dalam timestamp

// Helper
const ctx = (id) => document.getElementById(id)?.getContext("2d");

// Semua chart
const aircraftCountChart = new Chart(ctx("aircraftCountChart"), {
    type: "line",
    data: {
        labels,
        datasets: [
            {
                label: "Jumlah Pesawat",
                data: countData,
                borderColor: "blue",
                tension: 0.4,
            },
        ],
    },
    options: { responsive: true, maintainAspectRatio: false },
});

const avgAltitudeChart = new Chart(ctx("avgAltitudeChart"), {
    type: "line",
    data: {
        labels,
        datasets: [
            {
                label: "Rata-rata Altitude (ft)",
                data: altitudeData,
                borderColor: "green",
                tension: 0.4,
            },
        ],
    },
});

const avgSpeedChart = new Chart(ctx("avgSpeedChart"), {
    type: "line",
    data: {
        labels,
        datasets: [
            {
                label: "Rata-rata Kecepatan (knots)",
                data: speedData,
                borderColor: "orange",
                tension: 0.4,
            },
        ],
    },
});

const airlineChart = new Chart(ctx("airlineChart"), {
    type: "bar",
    data: {
        labels: [],
        datasets: [
            {
                label: "Jumlah Pesawat per Maskapai",
                data: [],
                backgroundColor: "purple",
            },
        ],
    },
});

const headingChart = new Chart(ctx("headingDistributionChart"), {
    type: "polarArea",
    data: {
        labels: Object.keys(headingGroups),
        datasets: [
            {
                label: "Distribusi Arah",
                data: Object.values(headingGroups),
                backgroundColor: ["#f00", "#0f0", "#00f", "#ff0"],
            },
        ],
    },
});

const altitudeChart = new Chart(ctx("altitudeDistributionChart"), {
    type: "bar",
    data: {
        labels: Object.keys(altitudeBins),
        datasets: [
            {
                label: "Distribusi Ketinggian",
                data: Object.values(altitudeBins),
                backgroundColor: "skyblue",
            },
        ],
    },
});

const regionChart = new Chart(ctx("regionChart"), {
    type: "bar",
    data: {
        labels: [],
        datasets: [
            {
                label: "Jumlah Pesawat per Wilayah",
                data: [],
                backgroundColor: "brown",
            },
        ],
    },
});

const speedExtremesChart = new Chart(ctx("speedExtremesChart"), {
    type: "line",
    data: {
        labels,
        datasets: [
            {
                label: "Kecepatan Maksimum",
                data: speedMaxData,
                borderColor: "red",
                tension: 0.4,
            },
            {
                label: "Kecepatan Minimum",
                data: speedMinData,
                borderColor: "black",
                tension: 0.4,
            },
        ],
    },
});

// Fungsi untuk menambahkan stat card ke container dan simpan ke localStorage
function addReportCard(waktu, jumlah, save = true) {
    const card = document.createElement("div");
    card.className = "bg-white rounded-lg shadow p-4 text-center";
    card.innerHTML = `
    <div class="text-sm text-gray-500">${waktu}</div>
    <div class="text-xl font-bold">${jumlah} Pesawat</div>
  `;

    const container = document.getElementById("reportCards");
    container.prepend(card); // Tambahkan paling depan

    if (container.children.length > 12) {
        container.removeChild(container.lastChild); // Hapus yang paling lama
    }

    if (save) {
        // Ambil semua kartu sekarang untuk disimpan
        const cards = [];
        container.querySelectorAll("div.bg-white").forEach((c) => {
            cards.push({
                waktu: c.querySelector("div.text-sm")?.textContent || "",
                jumlah:
                    c
                        .querySelector("div.text-xl")
                        ?.textContent.replace(" Pesawat", "") || "0",
            });
        });
        localStorage.setItem("flightReportCards", JSON.stringify(cards));
    }
}

// Load data kartu dari localStorage saat halaman dibuka
window.addEventListener("DOMContentLoaded", () => {
    const savedCards = localStorage.getItem("flightReportCards");
    if (savedCards) {
        try {
            const cards = JSON.parse(savedCards);
            cards.reverse().forEach(({ waktu, jumlah }) => {
                addReportCard(waktu, jumlah, false); // false supaya tidak double simpan
            });
        } catch (e) {
            console.warn("Gagal memuat data kartu dari localStorage:", e);
        }
    }
});

// Fungsi update tabel ringkasan jumlah pesawat per waktu
function updateSummaryTable(dateTime, count) {
    const tbody = document.getElementById("summaryTableBody");
    if (!tbody) return;

    const date = dateTime.toLocaleDateString();
    const time = dateTime.toLocaleTimeString();

    const row = document.createElement("tr");
    row.innerHTML = `
    <td class="border px-2 py-1">${date}</td>
    <td class="border px-2 py-1">${time}</td>
    <td class="border px-2 py-1 text-center">${count}</td>
  `;

    // Tambahkan paling atas
    tbody.prepend(row);

    // Hapus baris lebih dari 12
    while (tbody.rows.length > 12) {
        tbody.deleteRow(tbody.rows.length - 1);
    }
}

// WebSocket listener
ws.onmessage = function (event) {
    let flights;
    try {
        flights = JSON.parse(event.data);
        console.log("Data diterima:", flights);
    } catch (e) {
        console.error("❌ Gagal parsing data WebSocket:", e);
        return;
    }

    if (!Array.isArray(flights)) {
        console.warn("⚠️ Data bukan array:", flights);
        return;
    }

    const now = new Date();
    const total = flights.length;
    latestFlightCount = total;

    // Sidebar toggle
    document
        .querySelector("[data-menu]")
        .addEventListener("click", function () {
            const sidebar = document.getElementById("sidebar");
            const texts = document.querySelectorAll(".menu-text");

            sidebar.classList.toggle("w-64");
            sidebar.classList.toggle("w-20");

            texts.forEach((text) => {
                text.classList.toggle("hidden");
            });
        });

    // Update tabel ringkasan hanya jika 5 menit sudah berlalu
    const currentTime = now.getTime(); // Mendapatkan waktu sekarang dalam milidetik
    if (currentTime - lastUpdated >= 5 * 60 * 1000) {
        // Jika sudah 5 menit
        updateSummaryTable(now, total);
        lastUpdated = currentTime; // Update waktu pembaruan terakhir
    }

    const avgAltitude =
        flights.reduce((sum, a) => sum + (a.altitude || 0), 0) / total;
    const avgSpeed =
        flights.reduce((sum, a) => sum + (a.speed || 0), 0) / total;
    const maxSpeed = Math.max(...flights.map((f) => f.speed || 0));
    const minSpeed = Math.min(...flights.map((f) => f.speed || 0));

    labels.push(now.toLocaleTimeString());
    countData.push(total);
    altitudeData.push(avgAltitude.toFixed(2));
    speedData.push(avgSpeed.toFixed(2));
    speedMaxData.push(maxSpeed);
    speedMinData.push(minSpeed);

    if (labels.length > 10) {
        labels.shift();
        countData.shift();
        altitudeData.shift();
        speedData.shift();
        speedMaxData.shift();
        speedMinData.shift();
    }

    const airlines = {};
    const headingCount = { East: 0, South: 0, West: 0, North: 0 };
    const altitudeCount = { ...altitudeBins };
    const region = {};

    flights.forEach((f) => {
        const name = f.operator || (f.callsign || "").substring(0, 3);
        airlines[name] = (airlines[name] || 0) + 1;

        const heading = f.heading || 0;
        if (heading < 90) headingCount.East++;
        else if (heading < 180) headingCount.South++;
        else if (heading < 270) headingCount.West++;
        else headingCount.North++;

        const alt = f.altitude || 0;
        if (alt < 5000) altitudeCount["0-5000"]++;
        else if (alt < 10000) altitudeCount["5000-10000"]++;
        else if (alt < 15000) altitudeCount["10000-15000"]++;
        else if (alt < 20000) altitudeCount["15000-20000"]++;
        else altitudeCount["20000+"]++;

        const area = (f.callsign || "UNK").substring(0, 2);
        region[area] = (region[area] || 0) + 1;
    });

    airlineChart.data.labels = Object.keys(airlines);
    airlineChart.data.datasets[0].data = Object.values(airlines);

    headingChart.data.datasets[0].data = Object.values(headingCount);
    altitudeChart.data.datasets[0].data = Object.values(altitudeCount);
    regionChart.data.labels = Object.keys(region);
    regionChart.data.datasets[0].data = Object.values(region);

    aircraftCountChart.update();
    avgAltitudeChart.update();
    avgSpeedChart.update();
    airlineChart.update();
    headingChart.update();
    altitudeChart.update();
    regionChart.update();
    speedExtremesChart.update();
};

// Setiap 5 menit, buat laporan dalam bentuk kartu dan simpan
setInterval(() => {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    addReportCard(timeString, latestFlightCount);
}, 5 * 60 * 1000);
