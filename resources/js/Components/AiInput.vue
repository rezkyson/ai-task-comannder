<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { Sparkles, ArrowRight, Loader2, Info } from 'lucide-vue-next';

const emit = defineEmits(['parsed']);

const input = ref('');
const isLoading = ref(false);
const error = ref('');

const examples = [
    'Kerjakan laporan keuangan besok pagi',
    'Beli bahan masakan nanti sore',
    'Meeting klien 3 hari lagi, urgent',
];
const placeholder = ref(examples[0]);
let idx = 0;
setInterval(() => { idx = (idx + 1) % examples.length; placeholder.value = examples[idx]; }, 4000);

const parseInput = async () => {
    if (!input.value.trim()) return;
    isLoading.value = true;
    error.value = '';
    try {
        const res = await axios.post(route('tasks.parse'), { input: input.value });
        if (res.data.success) emit('parsed', { ...res.data.data, original_input: input.value });
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal memproses. Coba lagi.';
    } finally {
        isLoading.value = false;
    }
};

const handleKeydown = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); parseInput(); }
};
</script>

<template>
    <div>
        <div class="flex gap-2 items-end">
            <div class="flex-1 relative">
                <div class="absolute left-3 top-3 text-violet-500 dark:text-violet-400">
                    <Sparkles :size="16" />
                </div>
                <textarea v-model="input" @keydown="handleKeydown" :disabled="isLoading" rows="2"
                    :placeholder="placeholder + '...'"
                    class="w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:border-violet-400 dark:focus:border-violet-500 focus:ring-1 focus:ring-violet-100 dark:focus:ring-violet-500/20 outline-none resize-none transition-colors" />
            </div>
            <button @click="parseInput" :disabled="isLoading || !input.trim()"
                class="h-10 px-3 rounded-lg bg-violet-600 hover:bg-violet-700 disabled:bg-gray-200 dark:disabled:bg-gray-700 text-white disabled:text-gray-400 dark:disabled:text-gray-500 transition-colors flex items-center gap-1 text-sm font-medium flex-shrink-0">
                <Loader2 v-if="isLoading" :size="15" class="animate-spin" />
                <ArrowRight v-else :size="15" />
            </button>
        </div>
        <p class="flex items-center gap-1 text-[11px] text-gray-400 dark:text-gray-500 mt-1.5">
            <Info :size="11" />
            Tulis tugas dengan bahasa bebas, AI akan mengisi formulir otomatis
        </p>
        <p v-if="error" class="text-[12px] text-red-500 dark:text-red-400 mt-1.5">{{ error }}</p>
    </div>
</template>
