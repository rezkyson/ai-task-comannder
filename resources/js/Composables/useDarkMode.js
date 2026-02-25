import { ref, watch } from 'vue';

const isDark = ref(false);

// Init from localStorage / OS preference
const init = () => {
    isDark.value = localStorage.theme === 'dark' ||
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
    apply();
};

const apply = () => {
    document.documentElement.classList.toggle('dark', isDark.value);
};

const toggle = () => {
    isDark.value = !isDark.value;
    localStorage.theme = isDark.value ? 'dark' : 'light';
    apply();
};

init();

export function useDarkMode() {
    return { isDark, toggle };
}
