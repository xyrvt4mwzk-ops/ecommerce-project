import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

const savedTheme = localStorage.getItem('digitalmarket-theme');
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
const useDarkTheme = savedTheme ? savedTheme === 'dark' : prefersDark;

document.documentElement.classList.toggle('dark', useDarkTheme);

Alpine.store('theme', {
    dark: useDarkTheme,
    toggle() {
        this.dark = !this.dark;
        document.documentElement.classList.toggle('dark', this.dark);
        localStorage.setItem('digitalmarket-theme', this.dark ? 'dark' : 'light');
    },
});

Alpine.start();
