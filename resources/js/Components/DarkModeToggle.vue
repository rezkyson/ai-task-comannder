<script setup>
import { useDarkMode } from '@/Composables/useDarkMode';
import { Sun, Moon } from 'lucide-vue-next';

const { isDark, toggle } = useDarkMode();
</script>

<template>
    <button @click="toggle"
        class="p-1.5 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
        :title="isDark ? 'Light mode' : 'Dark mode'">
        <Moon v-if="isDark" :size="16" />
        <Sun v-else :size="16" />
    </button>
</template>
