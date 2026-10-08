// Import Chart.js
import {
  Chart, BarController, BarElement, LinearScale, CategoryScale, Tooltip, Legend,
} from 'chart.js';

// Import utilities
import { tailwindConfig, hexToRGB, hoverDataOnChart } from '../utils';

Chart.register(BarController, BarElement, LinearScale, CategoryScale, Tooltip, Legend);

// Plugin inline para dibujar el valor al final de cada barra horizontal
const valueOnTopPlugin = {
  id: 'valueOnTop',
  afterDatasetsDraw(chart) {
    const { ctx } = chart;
    chart.data.datasets.forEach((dataset, i) => {
      const meta = chart.getDatasetMeta(i);
      if (meta.hidden) return;
      meta.data.forEach((bar, index) => {
        const value = dataset.data[index];
        if (!value || value === 0) return;
        ctx.save();
        ctx.fillStyle = '#374151';
        ctx.font = '600 11px Inter, sans-serif';
        ctx.textAlign = 'left';
        ctx.textBaseline = 'middle';
        ctx.fillText(value, bar.x + 6, bar.y);
        ctx.restore();
      });
    });
  },
};

const dashboardCard16 = () => {
  const ctx = document.getElementById('dashboard-card-16');
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

  // Datos pre-calculados desde el backend (DashboardController::index)
  const result = window.dashboardChartData?.enviosPorEstado || { data: [], labels: [], modo: 'estados' };
  const dataset1 = result;
  const modoOficinas = result.modo === 'oficinas';

  // Actualizar título según el modo
  const titleEl = document.getElementById('dashboard-card-16-title');
  if (titleEl) {
    titleEl.textContent = modoOficinas
      ? 'Envíos recibidos por oficina (COP / OPT)'
      : 'Envíos recibidos por estado';
  }

  // Altura dinámica: 38px por barra, mínimo 160px, máximo 700px
  const wrapper = document.getElementById('dashboard-card-16-wrapper');
  if (wrapper) {
    const filas = Math.max(dataset1.labels.length, 1);
    const altura = Math.min(Math.max(filas * 38 + 40, 160), 700);
    wrapper.style.height = `${altura}px`;
  }

  const chart = new Chart(ctx, {
    type: 'bar',
    plugins: [valueOnTopPlugin],
    data: {
      labels: dataset1.labels,
      datasets: [
        {
          label: 'Envíos',
          data: dataset1.data,
          backgroundColor: '#6B1B3B',
          hoverBackgroundColor: '#8B2A4C',
          barPercentage: 0.9,
          categoryPercentage: 0.9,
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
          left: 12,
          right: 28,
        },
      },
      scales: {
        x: {
          display: true,
          beginAtZero: true,
          border: { display: false },
          ticks: {
            maxTicksLimit: 5,
            precision: 0,
            callback: (value) => hoverDataOnChart(value),
            color: darkMode ? textColor.dark : textColor.light,
          },
          grid: {
            display: true,
            color: darkMode ? gridColor.light : gridColor.dark,
          },
        },
        y: {
          display: true,
          border: { display: false },
          grid: {
            display: false,
          },
          ticks: {
            color: darkMode ? textColor.dark : textColor.light,
            font: { size: 11, weight: '500' },
            autoSkip: false,
          },
        },
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            title: (context) => context[0].label,
            label: (context) => `Envíos: ${hoverDataOnChart(context.parsed.x)}`,
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

export default dashboardCard16;
