const chartData = window.followedChartData;

const labels = chartData.map((item) => item.date);
const data = chartData.map((item) => item.total);

const ctx = document.getElementById("followedChart").getContext("2d");
const followedChart = new Chart(ctx, {
    type: "bar",
    data: {
        labels: labels,
        datasets: [
            {
                label: "Jumlah Pesawat di-Follow",
                data: data,
                backgroundColor: "rgba(75, 192, 192, 0.6)",
                borderColor: "rgba(75, 192, 192, 1)",
                borderWidth: 1,
            },
        ],
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                },
            },
        },
    },
});
