import './bootstrap';
import Alpine from 'alpinejs';
import Dropzone from 'dropzone';
import 'dropzone/dist/dropzone.css';

window.Alpine = Alpine;
window.Dropzone = Dropzone;

Alpine.start();

// ─── Theme Toggle ─────────────────────────────────────
window.toggleTheme = function (e) {
    e = e || window.event;
    const html = document.documentElement;
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    
    // Fallback for browsers without View Transitions or users preferring reduced motion
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!document.startViewTransition || prefersReducedMotion) {
        html.setAttribute('data-theme', next);
        localStorage.setItem('himti-theme', next);
        return;
    }

    // Get click coordinates (fallback to button center, then screen center)
    let x = innerWidth / 2;
    let y = innerHeight / 2;
    if (e) {
        if (e.clientX !== undefined && e.clientX !== 0) {
            x = e.clientX;
            y = e.clientY;
        } else if (e.target || e.currentTarget) {
            const rect = (e.currentTarget || e.target).getBoundingClientRect();
            x = rect.left + rect.width / 2;
            y = rect.top + rect.height / 2;
        }
    }

    const endRadius = Math.hypot(
        Math.max(x, innerWidth - x),
        Math.max(y, innerHeight - y)
    );
    html.style.setProperty('--theme-x', x + 'px');
    html.style.setProperty('--theme-y', y + 'px');
    html.classList.add('theme-transition');

    const transition = document.startViewTransition(() => {
        html.setAttribute('data-theme', next);
        localStorage.setItem('himti-theme', next);
    });

    transition.finished.then(() => {
        html.classList.remove('theme-transition');
    });
};



// ─── Toast Notification System ────────────────────────
window.showToast = function (type, title, msg) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'toast-enter border rounded-2xl transition-all duration-300';
    toast.style.cssText = 'background: var(--bg-panel); border-color: var(--ink-line-2); color: var(--paper); box-shadow: 0 16px 32px -8px rgba(0,0,0,0.4); display: flex; align-items: flex-start; gap: 14px; padding: 14px 16px; min-width: 300px; max-width: 400px;';

    const icons = {
        success: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        error: '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
        warning: '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
        info: '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
    };

    const bgColors = {
        success: 'var(--color-green, #10B981)',
        error: 'var(--color-red, #EF4444)',
        warning: 'var(--color-accent, #F59E0B)',
        info: 'var(--color-navy, #3B82F6)',
    };

    toast.innerHTML = `
        <div style="flex-shrink: 0; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: ${bgColors[type] || bgColors.info}; color: white; box-shadow: 0 4px 10px -2px ${bgColors[type] || bgColors.info}70;">
            <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">${icons[type] || icons.info}</svg>
        </div>
        <div style="flex: 1; padding-top: 1px;">
            <div style="font-weight: 600; font-size: 13.5px; letter-spacing: -0.1px; line-height: 1.3; margin-bottom: 4px; color: var(--paper);">${title}</div>
            <div style="font-size: 12px; color: var(--paper-dim); line-height: 1.5;">${msg}</div>
        </div>
        <button onclick="dismissToast(this.parentElement)" style="flex-shrink: 0; width: 24px; height: 24px; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; background: transparent; color: var(--paper-dim); margin-top: -2px; margin-right: -4px; transition: 0.2s;" onmouseover="this.style.background='var(--ink-line)'; this.style.color='var(--paper)'" onmouseout="this.style.background='transparent'; this.style.color='var(--paper-dim)'">
            <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>`;

    container.appendChild(toast);
    setTimeout(() => dismissToast(toast), 4000);
};

window.dismissToast = function (el) {
    if (!el || el.classList.contains('toast-hide')) return;
    el.classList.add('toast-hide');
    setTimeout(() => el.remove(), 300);
};
