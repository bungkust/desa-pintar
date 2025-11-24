/**
 * Chart.js Initialization Module
 * Handles all chart visualizations on APBDes page
 */

// Chart.js will be dynamically imported when needed
let Chart = null;

/**
 * Initialize all charts on APBDes page
 * Dynamically imports Chart.js only when needed
 */
export async function initApbdesCharts(chartData) {
    if (!chartData) {
        return;
    }

    // Dynamically import Chart.js only when needed
    if (!Chart) {
        const ChartModule = await import('chart.js/auto');
        Chart = ChartModule.default;
    }

    // Initialize Pie Chart for Pendapatan
    initPieChart('pendapatan-pie-chart', chartData.pieChartPendapatan, 'green');

    // Initialize Pie Chart for Belanja
    initPieChart('belanja-pie-chart', chartData.pieChartBelanja, 'red');

    // Initialize Bar Chart for Comparison
    initBarChart('comparison-bar-chart', chartData.barChartComparison);
}

/**
 * Initialize a pie chart
 */
function initPieChart(canvasId, data, colorScheme = 'blue') {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !data || !data.labels || data.labels.length === 0) {
        return;
    }

    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    const existingChart = Chart.getChart(ctx);
    if (existingChart) {
        existingChart.destroy();
    }

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: data.colors || generateColors(data.labels.length, colorScheme),
                borderWidth: 2,
                borderColor: '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(2);
                            return `${label}: Rp ${formatNumber(value)} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Initialize a bar chart for comparison
 */
function initBarChart(canvasId, data) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !data || !data.labels || data.labels.length === 0) {
        return;
    }

    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart if it exists
    const existingChart = Chart.getChart(ctx);
    if (existingChart) {
        existingChart.destroy();
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Anggaran',
                    data: data.anggaran,
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                },
                {
                    label: 'Realisasi',
                    data: data.realisasi,
                    backgroundColor: 'rgba(34, 197, 94, 0.6)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + formatNumber(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: Rp ${formatNumber(context.parsed.y)}`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Generate color array for charts
 */
function generateColors(count, colorScheme = 'blue') {
    const colorSchemes = {
        'green': ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0', '#d1fae5', '#ecfdf5'],
        'red': ['#ef4444', '#f87171', '#fca5a5', '#fecaca', '#fee2e2', '#fef2f2'],
        'blue': ['#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe', '#dbeafe', '#eff6ff'],
        'orange': ['#f97316', '#fb923c', '#fdba74', '#fed7aa', '#ffedd5', '#fff7ed'],
    };

    const scheme = colorSchemes[colorScheme] || colorSchemes['blue'];
    const colors = [];
    
    for (let i = 0; i < count; i++) {
        colors.push(scheme[i % scheme.length]);
    }

    return colors;
}

/**
 * Format number with thousand separators
 */
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(Math.round(num));
}

// Auto-initialize charts when DOM is ready if data is available (for APBDes page)
if (typeof document !== 'undefined') {
    function autoInitCharts() {
        const chartDataElement = document.getElementById('chart-data');
        if (chartDataElement) {
            try {
                const chartData = JSON.parse(chartDataElement.textContent);
                initApbdesCharts(chartData);
            } catch (e) {
                // Silently handle error - chart data is optional
                if (import.meta.env?.DEV) {
                    console.warn('Error parsing chart data:', e);
                }
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoInitCharts);
} else {
        autoInitCharts();
    }
}
