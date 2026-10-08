/**
 * Interactive Chart.js Initializer
 * Personal Expense Management System
 */

document.addEventListener("DOMContentLoaded", function () {
    // Check if Chart.js library is loaded
    if (typeof Chart === "undefined") {
        console.warn("Chart.js library is not available. Skipping chart renders.");
        return;
    }

    // Modern color palettes
    const palette = [
        "#3b82f6", "#ef4444", "#10b981", "#f59e0b", "#8b5cf6",
        "#ec4899", "#14b8a6", "#f97316", "#6366f1", "#84cc16",
        "#06b6d4", "#a855f7"
    ];

    // -------------------------------------------------------------
    // 1. Category-wise Expense Chart (Doughnut)
    // -------------------------------------------------------------
    const catCanvas = document.getElementById("categoryExpenseChart");
    if (catCanvas && window.categoryChartData && window.categoryChartData.labels.length > 0) {
        new Chart(catCanvas, {
            type: "doughnut",
            data: {
                labels: window.categoryChartData.labels,
                datasets: [{
                    data: window.categoryChartData.values,
                    backgroundColor: palette.slice(0, window.categoryChartData.labels.length),
                    borderWidth: 2,
                    borderColor: "#ffffff"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            boxWidth: 12,
                            padding: 12,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const val = context.raw || 0;
                                return " " + context.label + ": ₹" + Number(val).toLocaleString("en-IN", { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                cutout: "60%"
            }
        });
    }

    // -------------------------------------------------------------
    // 2. Weekly Daily Expense Chart (Bar Chart: Mon - Sun)
    // -------------------------------------------------------------
    const weekCanvas = document.getElementById("weeklyExpenseChart");
    if (weekCanvas && window.weeklyChartData) {
        new Chart(weekCanvas, {
            type: "bar",
            data: {
                labels: window.weeklyChartData.labels,
                datasets: [{
                    label: "Daily Expense (₹)",
                    data: window.weeklyChartData.values,
                    backgroundColor: "#3b82f6",
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (val) { return "₹" + val; }
                        },
                        grid: { color: "#f1f5f9" }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return " ₹" + Number(context.raw).toLocaleString("en-IN", { minimumFractionDigits: 2 });
                            }
                        }
                    }
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 3. Monthly Weekly Breakdown (Week 1 to Week 5)
    // -------------------------------------------------------------
    const monthCanvas = document.getElementById("monthlyExpenseChart");
    if (monthCanvas && window.monthlyChartData) {
        new Chart(monthCanvas, {
            type: "bar",
            data: {
                labels: window.monthlyChartData.labels,
                datasets: [{
                    label: "Weekly Expense (₹)",
                    data: window.monthlyChartData.values,
                    backgroundColor: "#8b5cf6",
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (val) { return "₹" + val; }
                        },
                        grid: { color: "#f1f5f9" }
                    },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return " ₹" + Number(context.raw).toLocaleString("en-IN", { minimumFractionDigits: 2 });
                            }
                        }
                    }
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 4. Payment Method Analysis Chart
    // -------------------------------------------------------------
    const payCanvas = document.getElementById("paymentMethodChart");
    if (payCanvas && window.paymentChartData && window.paymentChartData.labels.length > 0) {
        new Chart(payCanvas, {
            type: "doughnut",
            data: {
                labels: window.paymentChartData.labels,
                datasets: [{
                    data: window.paymentChartData.values,
                    backgroundColor: ["#10b981", "#3b82f6", "#f59e0b", "#ec4899", "#6366f1", "#94a3b8"],
                    borderWidth: 2,
                    borderColor: "#ffffff"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: { boxWidth: 12, padding: 12 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return " " + context.label + ": ₹" + Number(context.raw).toLocaleString("en-IN", { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                cutout: "55%"
            }
        });
    }

    // -------------------------------------------------------------
    // 5. Monthly Comparison Trend Chart (Income vs Expense)
    // -------------------------------------------------------------
    const trendCanvas = document.getElementById("monthlyComparisonChart");
    if (trendCanvas && window.monthlyTrendData) {
        new Chart(trendCanvas, {
            type: "bar",
            data: {
                labels: window.monthlyTrendData.labels,
                datasets: [
                    {
                        label: "Income (₹)",
                        data: window.monthlyTrendData.incomes,
                        backgroundColor: "#10b981",
                        borderRadius: 4
                    },
                    {
                        label: "Expense (₹)",
                        data: window.monthlyTrendData.expenses,
                        backgroundColor: "#ef4444",
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (val) { return "₹" + val; }
                        },
                        grid: { color: "#f1f5f9" }
                    },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { position: "top" },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return " " + context.dataset.label + ": ₹" + Number(context.raw).toLocaleString("en-IN", { minimumFractionDigits: 2 });
                            }
                        }
                    }
                }
            }
        });
    }
});
