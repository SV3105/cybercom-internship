// profile.js - Profile page functionality

document.addEventListener('DOMContentLoaded', function() {
    // Get chart data from the data attribute
    const chartCanvas = document.getElementById('orderChart');
    
    if (chartCanvas) {
        const chartDataElement = document.getElementById('chartData');
        const chartData = chartDataElement ? JSON.parse(chartDataElement.textContent) : [];
        
        // Only initialize chart if there's data
        if (chartData && chartData.length > 0) {
            const labels = chartData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
            });
            const amounts = chartData.map(item => parseFloat(item.amount));

            // Create chart
            const chartContext = chartCanvas.getContext('2d');
            
            // Gradient for the chart
            const gradient = chartContext.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.05)');

            new Chart(chartContext, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Spending',
                        data: amounts,
                        backgroundColor: '#6366f1',
                        hoverBackgroundColor: '#4f46e5',
                        borderRadius: 6,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#f8fafc',
                            bodyColor: '#f8fafc',
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return '₹' + context.parsed.y.toLocaleString('en-IN');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return '₹' + value.toLocaleString('en-IN');
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});
