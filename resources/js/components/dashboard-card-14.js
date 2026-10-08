// Import Chart.js
import {
    Chart, LineController, LineElement, Filler, PointElement, LinearScale, TimeScale, Tooltip,
} from 'chart.js';

// Import utilities
import { tailwindConfig, formatValue, hexToRGB, hoverDataOnChart } from '../utils';

Chart.register(LineController, LineElement, Filler, PointElement, LinearScale, TimeScale, Tooltip);

// A chart built with Chart.js 3
const dashboardCard14 = () => {
    const ctx = document.getElementById('dashboard-card-14');
    if (!ctx) return;

    const darkMode = localStorage.getItem('dark-mode') === 'true';

    const textColor = {
        light: '#9CA3AF',
        dark: '#6B7280'
    };

    const gridColor = {
        light: '#F3F4F6',
        dark: `rgba(${hexToRGB('#374151')}, 0.6)`
    };

    const tooltipBodyColor = {
        light: '#6B7280',
        dark: '#9CA3AF'
    };

    const tooltipBgColor = {
        light: '#ffffff',
        dark: '#374151'
    };

    const tooltipBorderColor = {
        light: '#E5E7EB',
        dark: '#4B5563'
    };

    // Usar datos pre-calculados con filtros del backend
    const result = window.dashboardChartData?.entregados || { todos: { data: [], labels: [] }, nacional: { data: [], labels: [] }, internacional: { data: [], labels: [] } };

    const dataset1 = result.todos.data;
    const dataset2 = result.nacional.data;
    const dataset3 = result.internacional.data;

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: result.todos.labels,
            datasets: [
                // Total line
                {
                    label: 'General',
                    data: dataset1,
                    borderColor: '#6b1820',
                    fill: false,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 3,
                    pointBackgroundColor: '#6b1820',
                    pointHoverBackgroundColor: '#6b1820',
                    pointBorderWidth: 0,
                    pointHoverBorderWidth: 0,
                    clip: 20,
                    tension: 0.2
                },
                // Nacional line
                {
                    label: 'Nacional',
                    data: dataset2,
                    borderColor: '#027172',
                    fill: false,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 3,
                    pointBackgroundColor: '#027172',
                    pointHoverBackgroundColor: '#027172',
                    pointBorderWidth: 0,
                    pointHoverBorderWidth: 0,
                    clip: 20,
                    tension: 0.2
                },
                // Internacional line
                {
                    label: 'Internacional',
                    data: dataset3,
                    borderColor: '#1D538B',
                    fill: false,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 3,
                    pointBackgroundColor: '#1D538B',
                    pointHoverBackgroundColor: '#1D538B',
                    pointBorderWidth: 0,
                    pointHoverBorderWidth: 0,
                    clip: 20,
                    tension: 0.2
                },
            ],
        },
        options: {
            layout: {
                padding: 20,
            },
            scales: {
                y: {
                    beginAtZero: true,
                    border: {
                        display: false,
                    },
                    ticks: {
                        maxTicksLimit: 5,
                        callback: (value) => hoverDataOnChart(value),
                        color: darkMode ? textColor.dark : textColor.light,
                    },
                    grid: {
                        color: darkMode ? gridColor.dark : gridColor.light,
                    },
                },
                x: {
                    border: {
                        display: false,
                    },
                    grid: {
                        display: false,
                    },
                    ticks: {
                        autoSkipPadding: 48,
                        maxRotation: 0,
                        color: darkMode ? textColor.dark : textColor.light,
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
                htmlLegend: {
                    // ID of the container to put the legend in
                    containerID: 'dashboard-card-14-legend',
                },
                tooltip: {
                    callbacks: {
                        title: () => false, // Disable tooltip title
                        label: (context) => `Entregados: ${hoverDataOnChart(context.parsed.y)}`,
                    },
                    bodyColor: darkMode ? tooltipBodyColor.dark : tooltipBodyColor.light,
                    backgroundColor: darkMode ? tooltipBgColor.dark : tooltipBgColor.light,
                    borderColor: darkMode ? tooltipBorderColor.dark : tooltipBorderColor.light,
                },
            },
            interaction: {
                intersect: false,
                mode: 'nearest',
            },
            maintainAspectRatio: false,
        },
        plugins: [{
            id: 'htmlLegend',
            afterUpdate(c, args, options) {
                const legendContainer = document.getElementById(options.containerID);
                const ul = legendContainer.querySelector('ul');
                if (!ul) return;
                // Remove old legend items
                while (ul.firstChild) {
                    ul.firstChild.remove();
                }
                // Reuse the built-in legendItems generator
                const items = c.options.plugins.legend.labels.generateLabels(c);
                items.slice(0, 3).forEach((item) => {
                    const li = document.createElement('li');
                    // Button element
                    const button = document.createElement('button');
                    button.style.display = 'inline-flex';
                    button.style.alignItems = 'center';
                    button.style.opacity = item.hidden ? '.3' : '';
                    button.onclick = () => {
                        c.setDatasetVisibility(item.datasetIndex, !c.isDatasetVisible(item.datasetIndex));
                        c.update();
                    };
                    // Color box
                    const box = document.createElement('span');
                    box.style.display = 'block';
                    box.style.width = tailwindConfig().theme.width[3];
                    box.style.height = tailwindConfig().theme.height[3];
                    box.style.borderRadius = tailwindConfig().theme.borderRadius.full;
                    box.style.marginRight = tailwindConfig().theme.margin[2];
                    box.style.borderWidth = '3px';
                    box.style.borderColor = c.data.datasets[item.datasetIndex].borderColor;
                    box.style.pointerEvents = 'none';
                    // Label
                    const label = document.createElement('span');
                    label.classList.add('text-gray-500', 'dark:text-gray-400');
                    label.style.fontSize = tailwindConfig().theme.fontSize.sm[0];
                    label.style.lineHeight = tailwindConfig().theme.fontSize.sm[1].lineHeight;
                    const labelText = document.createTextNode(item.text);
                    label.appendChild(labelText);
                    li.appendChild(button);
                    button.appendChild(box);
                    button.appendChild(label);
                    ul.appendChild(li);
                });
            },
        }],
    });

    document.addEventListener('darkMode', (e) => {
        const { mode } = e.detail;
        if (mode === 'on') {
            chart.options.scales.x.ticks.color = textColor.dark;
            chart.options.scales.y.ticks.color = textColor.dark;
            chart.options.scales.y.grid.color = gridColor.dark;
            chart.options.plugins.tooltip.bodyColor = tooltipBodyColor.dark;
            chart.options.plugins.tooltip.backgroundColor = tooltipBgColor.dark;
            chart.options.plugins.tooltip.borderColor = tooltipBorderColor.dark;
        } else {
            chart.options.scales.x.ticks.color = textColor.light;
            chart.options.scales.y.ticks.color = textColor.light;
            chart.options.scales.y.grid.color = gridColor.light;
            chart.options.plugins.tooltip.bodyColor = tooltipBodyColor.light;
            chart.options.plugins.tooltip.backgroundColor = tooltipBgColor.light;
            chart.options.plugins.tooltip.borderColor = tooltipBorderColor.light;
        }
        chart.update('none');
    });
};

export default dashboardCard14;
