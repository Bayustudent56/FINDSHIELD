import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', system-ui, sans-serif";
Chart.defaults.color = '#64748b';

window.Chart = Chart;
window.Alpine = Alpine;

Alpine.start();
