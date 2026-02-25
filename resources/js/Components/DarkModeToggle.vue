<script setup>
import { useDarkMode } from '@/Composables/useDarkMode';

const { isDark, toggle } = useDarkMode();
</script>

<template>
    <button
        @click="toggle"
        class="relative w-14 h-7 rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 dark:focus:ring-offset-slate-800"
        :class="isDark ? 'bg-indigo-500' : 'bg-slate-300'"
        :title="isDark ? 'Light mode' : 'Dark mode'"
    >
        <!-- Sliding circle -->
        <span
            class="absolute top-0.5 left-0.5 w-6 h-6 rounded-full bg-white shadow-sm flex items-center justify-center transition-transform duration-300"
            :class="isDark ? 'translate-x-7' : 'translate-x-0'"
        >
            <!-- Sun icon -->
            <svg v-if="!isDark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                <circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1" x2="12" y2="3"/>
                <line x1="12" y1="21" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="1" y1="12" x2="3" y2="12"/>
                <line x1="21" y1="12" x2="23" y2="12"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            </svg>
            <!-- Moon icon -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
        </span>
    </button>
</template>
