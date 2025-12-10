document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById("bookingChart");

    if (!window.chartData || !ctx) return;

    const labels = window.chartData.labels || [];
    const values = window.chartData.values || [];

    const gradient = ctx.getContext("2d").createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, "rgba(16, 185, 129, 0.9)");
    gradient.addColorStop(1, "rgba(16, 185, 129, 0.05)");

    const chart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [{
                label: "Jumlah",
                data: values,
                borderColor: "#10b981",
                borderWidth: 3,
                backgroundColor: gradient,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: "#10b981",
                pointBorderColor: "#065f46",
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointStyle: "circle"
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: 20
            },
            scales: {
                x: {
                    ticks: {
                        color: "#1e293b",
                        font: { size: 12 }
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    ticks: {
                        color: "#475569",
                        font: { size: 12 }
                    },
                    grid: {
                        borderDash: [4, 4],
                        color: "#e2e8f0"
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: { size: 14 }
                    }
                },
                tooltip: {
                    backgroundColor: "#111827",
                    titleColor: "#fff",
                    bodyColor: "#e5e7eb",
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        label: ctx => "Jumlah: " + ctx.raw
                    }
                }
            },
            animation: {
                duration: 900,
                easing: "easeOutQuart"
            }
        }
    });
});
