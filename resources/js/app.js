import '@tabler/core/dist/js/tabler.min.js';
/*
|--------------------------------------------------------------------------
| Modo claro / oscuro
|--------------------------------------------------------------------------
*/
const initTheme = () => {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');

    if (!themeToggle || !themeIcon) {
        return;
    }

    const applyThemeIcon = (theme) => {
        if (theme === 'dark') {
            themeIcon.classList.remove('ti-moon');
            themeIcon.classList.add('ti-sun');
        } else {
            themeIcon.classList.remove('ti-sun');
            themeIcon.classList.add('ti-moon');
        }
    };

    const currentTheme =
        document.documentElement.getAttribute('data-bs-theme') || 'light';

    applyThemeIcon(currentTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme =
            document.documentElement.getAttribute('data-bs-theme') || 'light';

        const nextTheme =
            currentTheme === 'dark'
                ? 'light'
                : 'dark';

        document.documentElement.setAttribute(
            'data-bs-theme',
            nextTheme
        );

        localStorage.setItem(
            'theme',
            nextTheme
        );

        applyThemeIcon(nextTheme);
    });
};
/*
|--------------------------------------------------------------------------
| Alertas temporales
|--------------------------------------------------------------------------
*/
const initAutoDismissAlerts = () => {
    const alerts = document.querySelectorAll('.auto-dismiss-alert');

    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.style.transition = 'opacity 300ms ease';
            alert.style.opacity = '0';

            setTimeout(() => {
                alert.remove();
            }, 300);

        }, 3500);
    });
};
/*
|--------------------------------------------------------------------------
| Inicialización
|--------------------------------------------------------------------------
*/
const initApp = () => {
    initTheme();
    initAutoDismissAlerts();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}