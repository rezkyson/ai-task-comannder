<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { CheckCircle2, XCircle } from 'lucide-vue-next';

const flash = computed(() => usePage().props.flash);
const messages = ref([]);
let idCounter = 0;

const addMessage = (text, type) => {
    const id = ++idCounter;
    messages.value.push({ id, text, type });
    setTimeout(() => {
        messages.value = messages.value.filter(m => m.id !== id);
    }, type === 'error' ? 5000 : 3000);
};

const dismiss = (id) => {
    messages.value = messages.value.filter(m => m.id !== id);
};

watch(flash, (val) => {
    if (val?.success) addMessage(val.success, 'success');
    if (val?.error) addMessage(val.error, 'error');
}, { immediate: true });
</script>

<template>
    <div class="fixed top-3 right-3 z-[9999] flex flex-col gap-2 pointer-events-none">
        <TransitionGroup enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 translate-x-4" enter-to-class="opacity-100 translate-x-0"
            leave-active-class="transition-all duration-150 ease-in" leave-from-class="opacity-100"
            leave-to-class="opacity-0 translate-x-4">
            <div v-for="msg in messages" :key="msg.id" @click="dismiss(msg.id)"
                class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-medium shadow-md cursor-pointer max-w-xs pointer-events-auto"
                :class="msg.type === 'success'
                    ? 'bg-emerald-50 dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-500/30'
                    : 'bg-red-50 dark:bg-gray-800 text-red-700 dark:text-red-300 border border-red-200/80 dark:border-red-500/30'">
                <CheckCircle2 v-if="msg.type === 'success'" :size="16" />
                <XCircle v-else :size="16" />
                <span>{{ msg.text }}</span>
            </div>
        </TransitionGroup>
    </div>
</template>
