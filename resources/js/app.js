import '@tabler/core/dist/js/tabler.min.js';
import '@tabler/core/dist/js/tabler.min.js';

document.addEventListener('DOMContentLoaded', () => {
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
        const current =
            document.documentElement.getAttribute('data-bs-theme');

        const nextTheme = current === 'dark' ? 'light' : 'dark';

        document.documentElement.setAttribute(
            'data-bs-theme',
            nextTheme
        );

        localStorage.setItem('theme', nextTheme);

        applyThemeIcon(nextTheme);
    });
});