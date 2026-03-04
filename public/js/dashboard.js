// Dashboard Charts
document.addEventListener('DOMContentLoaded', function () {
    const data = window.__dashboardData || {};
    const labels = data.labels || [];
    const revenue = data.revenue || [];
    const expenses = data.expenses || [];
    const profit = data.profit || [];

    // Styling Variables
    const colors = {
        primary: '#fbbf24', // Amber 400
        secondary: '#f87171', // Red 400
        text: '#cbd5e1', // Slate 300
        grid: 'rgba(148, 163, 184, 0.1)' // Slate 400 with opacity
    };

    Chart.defaults.font.family = "'Outfit', sans-serif";
    Chart.defaults.color = colors.text;
    Chart.defaults.scale.grid.color = colors.grid;

    // Revenue Chart
    new Chart(document.getElementById('revenueExpensesChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Revenue',
                    data: revenue,
                    backgroundColor: '#fbbf24',
                    hoverBackgroundColor: '#f59e0b',
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Expenses',
                    data: expenses,
                    backgroundColor: '#f87171',
                    hoverBackgroundColor: '#ef4444',
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: { usePointStyle: true, boxWidth: 8, color: '#cbd5e1' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: colors.grid, borderColor: 'transparent' },
                    ticks: { callback: v => '₱' + v, color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            }
        }
    });

    // Profit Chart
    const profitCtx = document.getElementById('netProfitChart').getContext('2d');
    const gradient = profitCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Blue 500
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

    new Chart(profitCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Net Profit',
                data: profit,
                borderColor: '#60a5fa', // Blue 400
                backgroundColor: gradient,
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#1e293b', // Slate 800
                pointBorderColor: '#60a5fa',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: colors.grid, borderColor: 'transparent' },
                    ticks: { callback: v => '₱' + v, color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            }
        }
    });
});
