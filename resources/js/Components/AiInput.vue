<script setup>
import { ref } from 'vue';
import axios from 'axios';

const emit = defineEmits(['parsed']);

const input = ref('');
const isLoading = ref(false);
const error = ref('');
const showHint = ref(true);

const hints = [
    '"Kerjakan laporan keuangan besok pagi"',
    '"Beli bahan masakan untuk makan malam nanti"',
    '"Belajar Vue.js minggu depan, penting!"',
    '"Cek kesehatan gigi tanggal 28, urgent"',
    '"Meeting dengan klien 3 hari lagi"',
];

const currentHint = ref(hints[0]);
let hintIndex = 0;

setInterval(() => {
    hintIndex = (hintIndex + 1) % hints.length;
    currentHint.value = hints[hintIndex];
}, 3000);

const parseInput = async () => {
    if (!input.value.trim()) return;

    isLoading.value = true;
    error.value = '';
    showHint.value = false;

    try {
        const response = await axios.post(route('tasks.parse'), {
            input: input.value,
        });

        if (response.data.success) {
            emit('parsed', {
                ...response.data.data,
                original_input: input.value,
            });
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal memproses input. Silakan coba lagi.';
    } finally {
        isLoading.value = false;
    }
};

const handleKeydown = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        parseInput();
    }
};
</script>

<template>
    <div class="mb-6">
        <!-- Input container -->
        <div class="flex items-start gap-3 p-4 bg-gradient-to-br from-blue-50 to-violet-50 dark:from-indigo-500/10 dark:to-purple-500/10 border-2 border-indigo-200 dark:border-indigo-500/30 rounded-2xl transition-all focus-within:border-indigo-400 dark:focus-within:border-indigo-400 focus-within:shadow-[0_0_0_4px_rgba(129,140,248,0.15)]"
            :class="{ 'opacity-70': isLoading }">
            <!-- AI icon -->
            <div
                class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <path
                        d="M12 2a4 4 0 0 1 4 4v1a3 3 0 0 1 3 3v1a2 2 0 0 1-2 2h-1v4a4 4 0 0 1-8 0v-4H7a2 2 0 0 1-2-2v-1a3 3 0 0 1 3-3V6a4 4 0 0 1 4-4z" />
                    <circle cx="9" cy="13" r="1" />
                    <circle cx="15" cy="13" r="1" />
                </svg>
            </div>

            <!-- Textarea -->
            <textarea v-model="input" @keydown="handleKeydown"
                :placeholder="'Ketik tugas dengan bahasa bebas... contoh: ' + currentHint" :disabled="isLoading"
                rows="2"
                class="flex-1 border-none bg-transparent resize-none text-[0.95rem] leading-relaxed text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 outline-none font-[inherit]">
            </textarea>

            <!-- Submit button -->
            <button @click="parseInput" :disabled="isLoading || !input.trim()"
                class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white cursor-pointer transition-all hover:scale-105 hover:shadow-lg hover:shadow-indigo-300 dark:hover:shadow-indigo-800 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:shadow-none">
                <svg v-if="!isLoading" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-5 h-5">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                </svg>
                <svg v-else class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </button>
        </div>

        <!-- Hint -->
        <div v-if="showHint"
            class="flex items-center gap-2 mt-3 px-3 py-2 text-xs text-indigo-500 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-500/5 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 flex-shrink-0">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 16v-4" />
                <path d="M12 8h.01" />
            </svg>
            <span>AI akan otomatis mengisi judul, kategori, prioritas, dan deadline dari kalimat kamu</span>
        </div>

        <!-- Error -->
        <div v-if="error"
            class="mt-3 px-3 py-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg">
            {{ error }}
        </div>
    </div>
</template>
