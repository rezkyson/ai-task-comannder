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
    <Transition name="toast">
        <div v-if="show" class="toast" :class="'toast-' + type" @click="show = false">
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

<style scoped>
.toast {
    position: fixed;
    top: 1.25rem;
    right: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.875rem 1.25rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    cursor: pointer;
    z-index: 9999;
    max-width: 400px;
}

.toast-success {
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}

.toast-error {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.toast-enter-active {
    transition: all 0.3s ease;
}

.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}
</style>
