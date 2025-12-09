import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

/**
 * Global Toast Notification Function
 * Usage: showToast('Success message', 'success')
 * Types: 'success' (green), 'error' (red), 'warning' (yellow), 'info' (blue)
 */
window.showToast = function(message, type = 'success', duration = 4000) {
    const colorClasses = {
        'success': 'bg-green-500',
        'error': 'bg-red-600',
        'warning': 'bg-yellow-500',
        'info': 'bg-blue-500',
    };

    const colorClass = colorClasses[type] || colorClasses['success'];

    const toastDiv = document.createElement('div');
    toastDiv.className = `fixed top-4 right-4 ${colorClass} text-white px-4 py-3 rounded shadow-lg transition-opacity duration-500 z-50`;
    toastDiv.textContent = message;
    toastDiv.style.opacity = '1';

    document.body.appendChild(toastDiv);

    setTimeout(() => {
        toastDiv.style.opacity = '0';
        setTimeout(() => toastDiv.remove(), 500);
    }, duration);
};

// Auto-remove session-based toasts from layout
document.addEventListener('DOMContentLoaded', () => {
    const toast = document.querySelector('.fixed.top-4.right-4');
    if (toast) {
        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s ease';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }
});
