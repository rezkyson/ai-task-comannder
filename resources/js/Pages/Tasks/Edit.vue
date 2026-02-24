<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    task: Object,
    categories: Array,
});

const formatDatetimeLocal = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    const offset = date.getTimezoneOffset();
    const local = new Date(date.getTime() - offset * 60 * 1000);
    return local.toISOString().slice(0, 16);
};

const form = useForm({
    title: props.task.title || '',
    description: props.task.description || '',
    category_id: props.task.category_id || '',
    priority: props.task.priority || 'medium',
    status: props.task.status || 'pending',
    deadline: formatDatetimeLocal(props.task.deadline),
});

const submit = () => {
    form.put(route('tasks.update', props.task.id));
};

const deleteTask = () => {
    if (confirm('Yakin ingin menghapus tugas ini?')) {
        router.delete(route('tasks.destroy', props.task.id));
    }
};
</script>

<template>
    <Head :title="'Edit: ' + task.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="header-row">
                <h2 class="page-title">Edit Tugas</h2>
                <Link :href="route('tasks.index')" class="back-btn">
                    ← Kembali
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <!-- Original Input Info -->
                <div v-if="task.original_input" class="original-input-card">
                    <div class="original-label">Input asli dari AI:</div>
                    <div class="original-text">"{{ task.original_input }}"</div>
                </div>

                <!-- Edit Form -->
                <form @submit.prevent="submit" class="section-card">
                    <div class="form-grid">
                        <!-- Title -->
                        <div class="form-group full-width">
                            <label class="form-label">Judul Tugas <span class="required">*</span></label>
                            <input v-model="form.title" type="text" class="form-input" required />
                            <p v-if="form.errors.title" class="form-error">{{ form.errors.title }}</p>
                        </div>

                        <!-- Description -->
                        <div class="form-group full-width">
                            <label class="form-label">Deskripsi</label>
                            <textarea v-model="form.description" class="form-input" rows="3"></textarea>
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
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select v-model="form.status" class="form-input">
                                <option value="pending">Belum Mulai</option>
                                <option value="in_progress">Sedang Dikerjakan</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>

                        <!-- Deadline -->
                        <div class="form-group">
                            <label class="form-label">Deadline</label>
                            <input v-model="form.deadline" type="datetime-local" class="form-input" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="form-actions">
                        <button type="button" @click="deleteTask" class="delete-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <path d="M3 6h18"/>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                            </svg>
                            Hapus
                        </button>
                        <div class="form-actions-right">
                            <Link :href="route('tasks.index')" class="cancel-btn">Batal</Link>
                            <button type="submit" :disabled="form.processing" class="submit-btn">
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                            </button>
                        </div>
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
}

.back-btn:hover {
    color: #4f46e5;
}

.original-input-card {
    background: linear-gradient(135deg, #eff6ff, #f5f3ff);
    border: 1px solid #c7d2fe;
    border-radius: 0.75rem;
    padding: 1rem 1.25rem;
    margin-bottom: 1.25rem;
}

.original-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6366f1;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.original-text {
    font-size: 0.9rem;
    color: #4338ca;
    font-style: italic;
}

.section-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    padding: 1.5rem;
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
    justify-content: space-between;
    align-items: center;
    margin-top: 1.5rem;
    padding-top: 1.25rem;
    border-top: 1px solid #f1f5f9;
}

.form-actions-right {
    display: flex;
    gap: 0.75rem;
}

.delete-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    background: white;
    color: #dc2626;
    border: 1px solid #fecaca;
    border-radius: 0.625rem;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.delete-btn:hover {
    background: #fef2f2;
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

@media (max-width: 640px) {
    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
        gap: 0.75rem;
    }

    .form-actions-right {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>
