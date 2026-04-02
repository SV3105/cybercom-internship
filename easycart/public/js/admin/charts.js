/**
 * Admin Dashboard Charts
 * Handles Inventory and Revenue charts using Chart.js
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if Chart library and data are available
    if (typeof Chart === 'undefined' || !window.adminDashboardStats) {
        console.error('Chart.js or dashboard data missing.');
        return;
    }

    const stats = window.adminDashboardStats;

    // --- Inventory Stock Chart ---
    const inventoryEl = document.getElementById('inventoryChart');
    if (inventoryEl) {
        const ctx = inventoryEl.getContext('2d');
        const categoryData = stats.category_stock || [];
        
        const labels = categoryData.map(item => item.category || 'Uncategorized');
        const data = categoryData.map(item => parseInt(item.total_stock));
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Stock Quantity',
                    data: data,
                    backgroundColor: 'rgba(8, 145, 178, 0.6)',
                    borderColor: 'rgba(8, 145, 178, 1)',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: {
                                family: "'Inter', sans-serif",
                                size: 12
                            },
                            color: '#64748b'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: "'Inter', sans-serif",
                                size: 12,
                                weight: '500'
                            },
                            color: '#1e293b'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#0f172a',
                        titleFont: {
                            family: "'Montserrat', sans-serif",
                            size: 14,
                            weight: '700'
                        },
                        bodyFont: {
                            family: "'Inter', sans-serif",
                            size: 13
                        },
                        cornerRadius: 8,
                        displayColors: false
                    }
                }
            }
        });
    }

    // --- Revenue Trend Chart ---
    const revenueEl = document.getElementById('revenueChart');
    if (revenueEl) {
        const revenueCtx = revenueEl.getContext('2d');
        const revenueTrend = stats.revenue_trend || [];
        
        const revLabels = revenueTrend.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const revData = revenueTrend.map(item => parseFloat(item.revenue));
        
        const gradient = revenueCtx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revLabels,
                datasets: [{
                    label: 'Daily Revenue',
                    data: revData,
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#10b981',
                    pointHoverBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            callback: value => '₹' + value.toLocaleString(),
                            font: { size: 11 },
                            color: '#64748b'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#64748b',
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 10
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#0f172a',
                        titleFont: {
                            family: "'Montserrat', sans-serif",
                            size: 14,
                            weight: '700'
                        },
                        bodyFont: {
                            family: "'Inter', sans-serif",
                            size: 13
                        },
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₹' + context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2});
                            }
                        }
                    }
                }
            }
        });
    }
});
