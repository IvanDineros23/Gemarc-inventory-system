import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const toast = document.querySelector('.fixed.top-4.right-4');
    if (toast) {
        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s ease';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500); // Remove from DOM after fade-out
        }, 4000); // 4 seconds delay
    }
});
