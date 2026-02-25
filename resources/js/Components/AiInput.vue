<script setup>
import { ref, watch } from 'vue';
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
    <div class="ai-input-wrapper">
        <div class="ai-input-container" :class="{ 'is-loading': isLoading }">
            <div class="ai-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <path
                        d="M12 2a4 4 0 0 1 4 4v1a3 3 0 0 1 3 3v1a2 2 0 0 1-2 2h-1v4a4 4 0 0 1-8 0v-4H7a2 2 0 0 1-2-2v-1a3 3 0 0 1 3-3V6a4 4 0 0 1 4-4z" />
                    <circle cx="9" cy="13" r="1" />
                    <circle cx="15" cy="13" r="1" />
                </svg>
            </div>
            <textarea v-model="input" @keydown="handleKeydown"
                :placeholder="'Ketik tugas dengan bahasa bebas... contoh: ' + currentHint" :disabled="isLoading"
                rows="2" class="ai-textarea"></textarea>
            <button @click="parseInput" :disabled="isLoading || !input.trim()" class="ai-submit-btn">
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

        <div v-if="showHint" class="ai-hint">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 16v-4" />
                <path d="M12 8h.01" />
            </svg>
            <span>AI akan otomatis mengisi judul, kategori, prioritas, dan deadline dari kalimat kamu</span>
        </div>

        <div v-if="error" class="ai-error">
            {{ error }}
        </div>
    </div>
</template>

<style scoped>
.ai-input-wrapper {
    margin-bottom: 1.5rem;
}

.ai-input-container {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    background: linear-gradient(135deg, #eff6ff, #f5f3ff);
    border: 2px solid #c7d2fe;
    border-radius: 1rem;
    transition: all 0.3s ease;
}

.ai-input-container:focus-within {
    border-color: #818cf8;
    box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.15);
}

.ai-input-container.is-loading {
    opacity: 0.7;
}

.ai-icon {
    flex-shrink: 0;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 0.75rem;
    color: white;
}

.ai-textarea {
    flex: 1;
    border: none;
    background: transparent;
    resize: none;
    font-size: 0.95rem;
    line-height: 1.5;
    color: #1e293b;
    outline: none;
    font-family: inherit;
}

.ai-textarea::placeholder {
    color: #94a3b8;
}

.ai-submit-btn {
    flex-shrink: 0;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border: none;
    border-radius: 0.75rem;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
}

.ai-submit-btn:hover:not(:disabled) {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
}

.ai-submit-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.ai-hint {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.75rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    color: #6366f1;
    background: rgba(99, 102, 241, 0.05);
    border-radius: 0.5rem;
}

.ai-error {
    margin-top: 0.75rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
    color: #dc2626;
    background: #fef2f2;
    border-radius: 0.5rem;
    border: 1px solid #fecaca;
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}
</style>
