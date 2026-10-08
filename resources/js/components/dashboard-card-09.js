// Import Chart.js
import {
  Chart, BarController, BarElement, LinearScale, TimeScale, Tooltip, Legend,
} from 'chart.js';

// Import utilities
import { tailwindConfig, hexToRGB, hoverDataOnChart } from '../utils';

Chart.register(BarController, BarElement, LinearScale, TimeScale, Tooltip, Legend);

const dashboardCard09 = () => {
  const ctx = document.getElementById('dashboard-card-09');
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
  const result = window.dashboardChartData?.serviciosEnvios || { data: [], labels: [] };
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
          border: { display: true },
          beginAtZero: true,
          ticks: {
            maxTicksLimit: 5,
            callback: (value) => hoverDataOnChart(value),
            color: darkMode ? textColor.dark : textColor.light,
          },
          grid: {
            display: true,
            color: darkMode ? gridColor.light : gridColor.dark,
          },
        },
        x: {
          display: true,
          grid: {
            display: false,
          },
          ticks: {
            color: darkMode ? textColor.dark : textColor.light,
            maxRotation: 30,
            minRotation: 0,
            callback: function(value) {
              const label = this.getLabelForValue(value);
              return label.length > 14 ? label.substring(0, 14) + '…' : label;
            },
          },
        },
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            title: (context) => context[0].label,
            label: (context) => `Envíos: ${hoverDataOnChart(context.parsed.y)}`,
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

  // 🌙 Dark mode listener
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

export default dashboardCard09;
