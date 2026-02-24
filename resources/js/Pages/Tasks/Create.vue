<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AiInput from '@/Components/AiInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    title: '',
    description: '',
    original_input: '',
    category_id: '',
    priority: 'medium',
    deadline: '',
});

const aiParsed = ref(false);

const handleAiParsed = (data) => {
    console.log('AI Parsed Data received:', data);
    form.title = data.title || '';
    form.description = data.description || '';
    form.original_input = data.original_input || '';
    form.category_id = data.category_id ? Number(data.category_id) : '';
    form.priority = data.priority || 'medium';
    form.deadline = data.deadline ? formatDatetimeLocal(data.deadline) : '';
    aiParsed.value = true;
    console.log('Form after AI fill:', { title: form.title, category_id: form.category_id, priority: form.priority, deadline: form.deadline });
};

const formatDatetimeLocal = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    const offset = date.getTimezoneOffset();
    const local = new Date(date.getTime() - offset * 60 * 1000);
    return local.toISOString().slice(0, 16);
};

const submit = () => {
    if (!form.original_input) {
        form.original_input = form.title;
    }
    form.post(route('tasks.store'), {
        onSuccess: () => {
            form.reset();
            aiParsed.value = false;
        },
    });
};

const getCategoryColor = (categoryId) => {
    const category = props.categories.find(c => c.id === categoryId);
    return category ? category.color : '#6366f1';
};
</script>

<template>

    <Head title="Tambah Tugas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="header-row">
                <h2 class="page-title">Tambah Tugas Baru</h2>
                <Link :href="route('tasks.index')" class="back-btn">
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <!-- AI Input Section -->
                <div class="section-card ai-section">
                    <div class="section-header">
                        <div class="section-icon ai-gradient">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="w-5 h-5">
                                <path
                                    d="M12 2a4 4 0 0 1 4 4v1a3 3 0 0 1 3 3v1a2 2 0 0 1-2 2h-1v4a4 4 0 0 1-8 0v-4H7a2 2 0 0 1-2-2v-1a3 3 0 0 1 3-3V6a4 4 0 0 1 4-4z" />
                                <circle cx="9" cy="13" r="1" />
                                <circle cx="15" cy="13" r="1" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">AI Smart Input</h3>
                            <p class="section-subtitle">Ketik tugas dengan bahasa bebas, AI akan mengisi form otomatis
                            </p>
                        </div>
                    </div>
                    <AiInput @parsed="handleAiParsed" />
                </div>

                <!-- Success Indicator -->
                <div v-if="aiParsed" class="ai-success">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    <span>AI berhasil mengisi form! Cek & edit jika perlu, lalu klik "Simpan Tugas"</span>
                </div>

                <!-- Manual Form -->
                <form @submit.prevent="submit" class="section-card form-section">
                    <div class="section-header">
                        <div class="section-icon form-gradient">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="w-5 h-5">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="section-title">Detail Tugas</h3>
                            <p class="section-subtitle">Edit atau isi manual detail tugas</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <!-- Title -->
                        <div class="form-group full-width">
                            <label class="form-label">Judul Tugas <span class="required">*</span></label>
                            <input v-model="form.title" type="text" class="form-input" placeholder="Judul tugas..."
                                required />
                            <p v-if="form.errors.title" class="form-error">{{ form.errors.title }}</p>
                        </div>

                        <!-- Description -->
                        <div class="form-group full-width">
                            <label class="form-label">Deskripsi</label>
                            <textarea v-model="form.description" class="form-input" rows="3"
                                placeholder="Deskripsi tambahan (opsional)..."></textarea>
                            <p v-if="form.errors.description" class="form-error">{{ form.errors.description }}</p>
                        </div>

                        <!-- Category -->
                        <div class="form-group">
                            <label class="form-label">Kategori</label>
                            <select v-model="form.category_id" class="form-input">
                                <option value="">Pilih Kategori</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.category_id" class="form-error">{{ form.errors.category_id }}</p>
                        </div>

                        <!-- Priority -->
                        <div class="form-group">
                            <label class="form-label">Prioritas</label>
                            <select v-model="form.priority" class="form-input">
                                <option value="low">🟢 Rendah</option>
                                <option value="medium">🟡 Sedang</option>
                                <option value="high">🟠 Tinggi</option>
                                <option value="urgent">🔴 Urgen</option>
                            </select>
                            <p v-if="form.errors.priority" class="form-error">{{ form.errors.priority }}</p>
                        </div>

                        <!-- Deadline -->
                        <div class="form-group full-width">
                            <label class="form-label">Deadline</label>
                            <input v-model="form.deadline" type="datetime-local" class="form-input" />
                            <p v-if="form.errors.deadline" class="form-error">{{ form.errors.deadline }}</p>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="form-actions">
                        <Link :href="route('tasks.index')" class="cancel-btn">Batal</Link>
                        <button type="submit" :disabled="form.processing" class="submit-btn">
                            <svg v-if="form.processing" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                                </path>
                            </svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Tugas' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
}

.back-btn {
    color: #6366f1;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.2s ease;
}

.back-btn:hover {
    color: #4f46e5;
}

.section-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1.25rem;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    margin-bottom: 1.25rem;
}

.section-icon {
    flex-shrink: 0;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    color: white;
}

.ai-gradient {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
}

.form-gradient {
    background: linear-gradient(135deg, #3b82f6, #06b6d4);
}

.section-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.section-subtitle {
    font-size: 0.8rem;
    color: #94a3b8;
    margin: 0.125rem 0 0;
}

.ai-success {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.875rem 1rem;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 0.75rem;
    color: #16a34a;
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 1.25rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.full-width {
    grid-column: 1 / -1;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.form-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
}

.required {
    color: #ef4444;
}

.form-input {
    padding: 0.625rem 0.875rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.625rem;
    font-size: 0.875rem;
    outline: none;
    transition: all 0.2s ease;
    background: white;
    width: 100%;
    font-family: inherit;
}

.form-input:focus {
    border-color: #818cf8;
    box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.15);
}

.form-error {
    font-size: 0.75rem;
    color: #dc2626;
    margin: 0;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1.5rem;
    padding-top: 1.25rem;
    border-top: 1px solid #f1f5f9;
}

.cancel-btn {
    padding: 0.625rem 1.25rem;
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #e2e8f0;
    border-radius: 0.625rem;
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.2s ease;
}

.cancel-btn:hover {
    background: #f1f5f9;
}

.submit-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.625rem 1.5rem;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
    border-radius: 0.625rem;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
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

@media (max-width: 640px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
