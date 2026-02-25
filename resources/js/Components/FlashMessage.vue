<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { CheckCircle2, XCircle } from 'lucide-vue-next';

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
    <Transition enter-active-class="transition-all duration-200 ease-out"
        enter-from-class="opacity-0 translate-y-[-8px]" enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-150 ease-in" leave-from-class="opacity-100"
        leave-to-class="opacity-0 translate-y-[-8px]">
        <div v-if="show" @click="show = false"
            class="fixed top-3 right-3 z-[9999] flex items-center gap-2 px-4 py-2.5 rounded-lg text-[13px] font-medium shadow-md cursor-pointer max-w-xs"
            :class="type === 'success'
                ? 'bg-emerald-50 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-500/20'
                : 'bg-red-50 dark:bg-red-500/15 text-red-700 dark:text-red-400 border border-red-200/80 dark:border-red-500/20'">
            <CheckCircle2 v-if="type === 'success'" :size="16" />
            <XCircle v-else :size="16" />
            <span>{{ message }}</span>
        </div>
    </Transition>
</template>
