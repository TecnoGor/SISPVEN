import { Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Import Tom Select
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';
window.TomSelect = TomSelect;

Livewire.start()

// ── Sesión expirada (419) en peticiones de Livewire ──
// Livewire por defecto muestra un popup nativo en inglés ("This page has
// expired..."). Lo interceptamos para, en vez de eso, mandar al usuario al
// login limpio con el aviso de sesión expirada (mismo comportamiento que el
// Handler de PHP para peticiones normales).
document.addEventListener('livewire:init', () => {
    Livewire.hook('request', ({ fail }) => {
        fail(({ status, preventDefault }) => {
            if (status === 419) {
                preventDefault();
                window.location.href = '/login?expirado=1';
            }
        });
    });
});

// ── Auto-logout por inactividad ──
// Cierra la sesión y lleva al login SOLO cuando el usuario lleva demasiado
// tiempo sin interactuar, sin que tenga que recargar ni hacer clic. El tiempo
// se toma del <meta name="session-lifetime"> que el layout expone a partir de
// SESSION_LIFETIME (config/session.php). Solo se activa si ese meta existe,
// es decir, cuando hay un usuario autenticado (@auth en el layout).
(function iniciarAutoLogout() {
    const meta = document.querySelector('meta[name="session-lifetime"]');
    if (!meta) return; // no autenticado (login, etc.) → no aplica

    const minutos = parseInt(meta.getAttribute('content'), 10);
    if (!minutos || minutos < 1) return;

    const limiteMs = minutos * 60 * 1000;
    let temporizador;

    const cerrarPorInactividad = () => {
        window.location.href = '/login?expirado=1';
    };

    const reiniciar = () => {
        clearTimeout(temporizador);
        temporizador = setTimeout(cerrarPorInactividad, limiteMs);
    };

    // Cualquier señal de actividad real reinicia el contador.
    ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click']
        .forEach((evento) => window.addEventListener(evento, reiniciar, { passive: true }));

    reiniciar(); // arranca el conteo al cargar la página
})();

// ── Utilidad global: Formato bancario venezolano (1.234,56) ──
// Disponible en cualquier blade como: oninput="formatCurrency(this)"
window.formatCurrency = function (input) {
    // Eliminar caracteres no numéricos
    let value = input.value.replace(/[^0-9]/g, '');

    // Si está vacío, mostrar 0,00
    if (value.length === 0) {
        input.value = '0,00';
        return;
    }

    // Convertir a centimos
    let cents = parseInt(value, 10);

    // Formatear a bs y centimos
    let bs = Math.floor(cents / 100);
    let formattedCents = (cents % 100).toString().padStart(2, '0');

    // Agregar separador de miles
    let formattedBs = bs.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Actualizar el valor del input
    input.value = `${formattedBs},${formattedCents}`;
};

import './bootstrap';


// Import Chart.js
import { Chart } from 'chart.js';

// Import flatpickr
import flatpickr from 'flatpickr';

// import component from './components/component';
import dashboardCard01 from './components/dashboard-card-01';
import dashboardCard02 from './components/dashboard-card-02';
import dashboardCard03 from './components/dashboard-card-03';
import dashboardCard04 from './components/dashboard-card-04';
import dashboardCard05 from './components/dashboard-card-05';
import dashboardCard06 from './components/dashboard-card-06';
import dashboardCard07 from './components/dashboard-card-07';
import dashboardCard08 from './components/dashboard-card-08';
import dashboardCard09 from './components/dashboard-card-09';
import dashboardCard10 from './components/dashboard-card-10';
import dashboardCard11 from './components/dashboard-card-11';
import dashboardCard12 from './components/dashboard-card-12';
import dashboardCard13 from './components/dashboard-card-13';
import dashboardCard14 from './components/dashboard-card-14';
import dashboardCard15 from './components/dashboard-card-15';
import dashboardCard16 from './components/dashboard-card-16';
import 'toastr/build/toastr.min.js';
import 'toastr/build/toastr.min.css';

// Define Chart.js default settings
/* eslint-disable prefer-destructuring */
Chart.defaults.font.family = '"Inter", sans-serif';
Chart.defaults.font.weight = 500;
Chart.defaults.plugins.tooltip.borderWidth = 1;
Chart.defaults.plugins.tooltip.displayColors = false;
Chart.defaults.plugins.tooltip.mode = 'nearest';
Chart.defaults.plugins.tooltip.intersect = false;
Chart.defaults.plugins.tooltip.position = 'nearest';
Chart.defaults.plugins.tooltip.caretSize = 0;
Chart.defaults.plugins.tooltip.caretPadding = 20;
Chart.defaults.plugins.tooltip.cornerRadius = 8;
Chart.defaults.plugins.tooltip.padding = 8;

// Function that generates a gradient for line charts
export const chartAreaGradient = (ctx, chartArea, colorStops) => {
  if (!ctx || !chartArea || !colorStops || colorStops.length === 0) {
    return 'transparent';
  }
  const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
  colorStops.forEach(({ stop, color }) => {
    gradient.addColorStop(stop, color);
  });
  return gradient;
};

// Register Chart.js plugin to add a bg option for chart area
Chart.register({
  id: 'chartAreaPlugin',
  // eslint-disable-next-line object-shorthand
  beforeDraw: (chart) => {
    if (chart.config.options.chartArea && chart.config.options.chartArea.backgroundColor) {
      const ctx = chart.canvas.getContext('2d');
      const { chartArea } = chart;
      ctx.save();
      ctx.fillStyle = chart.config.options.chartArea.backgroundColor;
      // eslint-disable-next-line max-len
      ctx.fillRect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
      ctx.restore();
    }
  },
});

document.addEventListener('DOMContentLoaded', () => {
  // Light switcher
  const lightSwitches = document.querySelectorAll('.light-switch');
  if (lightSwitches.length > 0) {
    lightSwitches.forEach((lightSwitch, i) => {
      if (localStorage.getItem('dark-mode') === 'true') {
        lightSwitch.checked = true;
      }
      lightSwitch.addEventListener('change', () => {
        const { checked } = lightSwitch;
        lightSwitches.forEach((el, n) => {
          if (n !== i) {
            el.checked = checked;
          }
        });
        document.documentElement.classList.add('[&_*]:!transition-none');
        if (lightSwitch.checked) {
          document.documentElement.classList.add('dark');
          document.querySelector('html').style.colorScheme = 'dark';
          localStorage.setItem('dark-mode', true);
          document.dispatchEvent(new CustomEvent('darkMode', { detail: { mode: 'on' } }));
        } else {
          document.documentElement.classList.remove('dark');
          document.querySelector('html').style.colorScheme = 'light';
          localStorage.setItem('dark-mode', false);
          document.dispatchEvent(new CustomEvent('darkMode', { detail: { mode: 'off' } }));
        }
        setTimeout(() => {
          document.documentElement.classList.remove('[&_*]:!transition-none');
        }, 1);
      });
    });
  }
  // Flatpickr
  flatpickr('.datepicker', {
    mode: 'range',
    static: true,
    monthSelectorType: 'static',
    dateFormat: 'M j, Y',
    defaultDate: [new Date().setDate(new Date().getDate() - 6), new Date()],
    prevArrow: '<svg class="fill-current" width="7" height="11" viewBox="0 0 7 11"><path d="M5.4 10.8l1.4-1.4-4-4 4-4L5.4 0 0 5.4z" /></svg>',
    nextArrow: '<svg class="fill-current" width="7" height="11" viewBox="0 0 7 11"><path d="M1.4 10.8L0 9.4l4-4-4-4L1.4 0l5.4 5.4z" /></svg>',
    onReady: (selectedDates, dateStr, instance) => {
      // eslint-disable-next-line no-param-reassign
      instance.element.value = dateStr.replace('to', '-');
      const customClass = instance.element.getAttribute('data-class');
      instance.calendarContainer.classList.add(customClass);
    },
    onChange: (selectedDates, dateStr, instance) => {
      // eslint-disable-next-line no-param-reassign
      instance.element.value = dateStr.replace('to', '-');
    },
  });
  dashboardCard01();
  dashboardCard02();
  dashboardCard03();
  dashboardCard04();
  dashboardCard05();
  dashboardCard06();
  dashboardCard08();
  dashboardCard09();
  dashboardCard07();
  dashboardCard10();
  dashboardCard11();
  dashboardCard12();
  dashboardCard13();
  dashboardCard14();
  dashboardCard15();
  dashboardCard16();
});
