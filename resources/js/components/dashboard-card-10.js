// Import Chart.js
import {
  Chart, BarController, BarElement, LinearScale, TimeScale, Tooltip, Legend,
} from 'chart.js';

// Import utilities
import { tailwindConfig, hexToRGB, hoverDataOnChart } from '../utils';

Chart.register(BarController, BarElement, LinearScale, TimeScale, Tooltip, Legend);

const dashboardCard10 = () => {
  const ctx = document.getElementById('dashboard-card-10');
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
  const result = window.dashboardChartData?.serviciosIngresos || { data: [], labels: [] };
  const dataset1 = result;

  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: dataset1.labels,
      datasets: [
        {
          label: 'Stack 1',
          data: dataset1.data,
          backgroundColor: '#6B1B3B',
          hoverBackgroundColor: '#6B1B3B',
          barPercentage: 0.7,
          categoryPercentage: 0.7,
          borderRadius: 4,
        },
      ],
    },
    options: {
      indexAxis: 'y',
      layout: {
        padding: {
          top: 12,
          bottom: 16,
          left: 20,
          right: 20,
        },
      },
      scales: {
        y: {
          stacked: true,
          display: true,
          border: { display: false },
          beginAtZero: true,
          ticks: {
            color: darkMode ? textColor.dark : textColor.light,
            callback: function(value) {
              const label = this.getLabelForValue(value);
              return label.length > 16 ? label.substring(0, 16) + '…' : label;
            },
          },
          grid: {
            display: false,
          },
        },
        x: {
          display: true,
          grid: {
            display: true,
            lineWidth: 1,
            drawBorder: false,
            color: darkMode ? gridColor.light : gridColor.dark,
          },
          ticks: {
            maxTicksLimit: 5,
            callback: (value) => hoverDataOnChart(value),
            color: darkMode ? textColor.dark : textColor.light,
          },
        },
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            title: (context) => context[0].label,
            label: (context) => `Ingresos: ${hoverDataOnChart(context.parsed.x)} Bs.`,
          },
          bodyColor: darkMode ? tooltipBodyColor.dark : tooltipBodyColor.light,
          titleColor: darkMode ? tooltipBodyColor.dark : tooltipBodyColor.light,
          backgroundColor: darkMode ? tooltipBgColor.dark : tooltipBgColor.light,
          borderColor: darkMode ? tooltipBorderColor.dark : tooltipBorderColor.light,
        },
      },
      interaction: {
        intersect: false,
        mode: 'nearest',
      },
      animation: {
        duration: 200,
      },
      maintainAspectRatio: false,
    },
  });

  document.addEventListener('darkMode', (e) => {
    const { mode } = e.detail;
    const isDark = mode === 'on';
    chart.options.scales.x.ticks.color = isDark ? textColor.dark : textColor.light;
    chart.options.scales.y.ticks.color = isDark ? textColor.dark : textColor.light;
    chart.options.scales.y.grid.color = isDark ? gridColor.dark : gridColor.light;
    chart.options.plugins.tooltip.bodyColor = isDark ? tooltipBodyColor.dark : tooltipBodyColor.light;
    chart.options.plugins.tooltip.backgroundColor = isDark ? tooltipBgColor.dark : tooltipBgColor.light;
    chart.options.plugins.tooltip.borderColor = isDark ? tooltipBorderColor.dark : tooltipBorderColor.light;
    chart.update('none');
  });
};

export default dashboardCard10;
