const ws = new WebSocket("ws://localhost:8080");

// Global arrays
const labels = [];
const countData = [];
const altitudeData = [];
const speedData = [];
const speedMaxData = [];
const speedMinData = [];

// 🔵 Tambahan untuk laporan 5 menit
const reportLabels = [];
const reportCounts = [];

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

// Helper
const ctx = (id) => document.getElementById(id)?.getContext("2d");

// Semua chart (termasuk yang baru)
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

// 🟩 Tambahan: Chart Laporan 5 Menit
const fiveMinuteReportChart = new Chart(ctx("fiveMinuteReportChart"), {
    type: "bar",
    data: {
        labels: reportLabels,
        datasets: [
            {
                label: "Laporan Jumlah Pesawat Tiap 5 Menit",
                data: reportCounts,
                backgroundColor: "teal",
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 5,
                },
            },
        },
    },
});

// WebSocket listener
ws.onmessage = function (event) {
    let data;
    try {
        data = JSON.parse(event.data);
    } catch (e) {
        console.error("❌ Gagal parsing data WebSocket:", e);
        return;
    }

    // 🔍 Cek jika data adalah laporan 5 menit
    if (data?.type === "report_5min") {
        const waktu = new Date(data.timestamp).toLocaleTimeString();
        reportLabels.push(waktu);
        reportCounts.push(data.count);

        // Batasi hanya 12 data terakhir
        if (reportLabels.length > 12) {
            reportLabels.shift();
            reportCounts.shift();
        }

        fiveMinuteReportChart.update();
        return;
    }

    // ✅ Proses data pesawat real-time
    if (!Array.isArray(data)) return;

    const now = new Date().toLocaleTimeString();
    const total = data.length;
    const avgAltitude =
        data.reduce((sum, a) => sum + (a.altitude || 0), 0) / total;
    const avgSpeed = data.reduce((sum, a) => sum + (a.speed || 0), 0) / total;
    const maxSpeed = Math.max(...data.map((f) => f.speed || 0));
    const minSpeed = Math.min(...data.map((f) => f.speed || 0));

    labels.push(now);
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

    // Maskapai, arah, ketinggian, wilayah
    const airlines = {};
    const headingCount = { East: 0, South: 0, West: 0, North: 0 };
    const altitudeCount = { ...altitudeBins };
    const region = {};

    data.forEach((f) => {
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

    // Update chart
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

// Sidebar toggle
document.querySelector("[data-menu]").addEventListener("click", function () {
    const sidebar = document.getElementById("sidebar");
    const texts = document.querySelectorAll(".menu-text");

    sidebar.classList.toggle("w-64");
    sidebar.classList.toggle("w-20");

    texts.forEach((text) => {
        text.classList.toggle("hidden");
    });
});
