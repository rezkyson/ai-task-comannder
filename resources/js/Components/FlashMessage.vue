<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const flash = computed(() => usePage().props.flash);
const show = ref(false);
const message = ref('');
const type = ref('success');

watch(flash, (val) => {
    if (val?.success) {
        message.value = val.success;
        type.value = 'success';
        show.value = true;
        setTimeout(() => show.value = false, 3000);
    }
    if (val?.error) {
        message.value = val.error;
        type.value = 'error';
        show.value = true;
        setTimeout(() => show.value = false, 5000);
    }
}, { immediate: true });
</script>

<template>
    <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 translate-x-full"
        enter-to-class="opacity-100 translate-x-0" leave-active-class="transition-all duration-300 ease-in"
        leave-from-class="opacity-100 translate-x-0" leave-to-class="opacity-0 translate-x-full">
        <div v-if="show" @click="show = false"
            class="fixed top-5 right-5 z-[9999] flex items-center gap-2.5 px-5 py-3.5 rounded-xl text-sm font-medium shadow-lg cursor-pointer max-w-sm"
            :class="type === 'success'
                ? 'bg-green-50 dark:bg-green-500/15 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-500/30 shadow-green-100 dark:shadow-black/20'
                : 'bg-red-50 dark:bg-red-500/15 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-500/30 shadow-red-100 dark:shadow-black/20'">
            <svg v-if="type === 'success'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="w-5 h-5 flex-shrink-0">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 flex-shrink-0">
                <circle cx="12" cy="12" r="10" />
                <line x1="15" y1="9" x2="9" y2="15" />
                <line x1="9" y1="9" x2="15" y2="15" />
            </svg>
            <span>{{ message }}</span>
        </div>
    </Transition>
</template>
