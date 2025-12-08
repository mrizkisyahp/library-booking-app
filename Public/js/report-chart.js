document.addEventListener("DOMContentLoaded", () => {
    if (!window.chartData) {
        console.error("chartData tidak ditemukan");
        return;
    }

    console.log("ChartData:", window.chartData);

    const canvas = document.getElementById("bookingChart");
    if (!canvas) {
        console.error("Canvas bookingChart tidak ditemukan");
        return;
    }

    new Chart(canvas, {
        type: "line",
        data: {
            labels: window.chartData.labels,
            datasets: [
                {
                    label: "Jumlah Booking",
                    data: window.chartData.values,
                    borderColor: "rgb(16 185 129)", // emerald-500
                    backgroundColor: "rgba(16, 185, 129, 0.2)",
                    borderWidth: 3,
                    tension: 0.3,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
