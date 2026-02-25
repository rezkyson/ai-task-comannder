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
    form.title = data.title || '';
    form.description = data.description || '';
    form.original_input = data.original_input || '';
    form.category_id = data.category_id ? Number(data.category_id) : '';
    form.priority = data.priority || 'medium';
    form.deadline = data.deadline ? formatDatetimeLocal(data.deadline) : '';
    aiParsed.value = true;
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
</script>

<template>

    <Head title="Tambah Tugas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Tambah Tugas Baru</h2>
                <Link :href="route('tasks.index')"
                    class="text-sm font-medium text-indigo-500 hover:text-indigo-700 dark:hover:text-indigo-400">←
                    Kembali</Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <!-- AI Input Section -->
                <div
                    class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 mb-5">
                    <div class="flex items-center gap-3.5 mb-5">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white flex-shrink-0">
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
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">AI Smart Input</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Ketik tugas dengan bahasa bebas, AI
                                akan
                                mengisi form otomatis</p>
                        </div>
                    </div>
                    <AiInput @parsed="handleAiParsed" />
                </div>

                <!-- Success Indicator -->
                <div v-if="aiParsed"
                    class="flex items-center gap-2.5 px-4 py-3 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/30 rounded-xl text-green-600 dark:text-green-400 text-sm font-medium mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    <span>AI berhasil mengisi form! Cek & edit jika perlu, lalu klik "Simpan Tugas"</span>
                </div>

                <!-- Manual Form -->
                <form @submit.prevent="submit"
                    class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6">
                    <div class="flex items-center gap-3.5 mb-5">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="w-5 h-5">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">Detail Tugas</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Edit atau isi manual detail tugas</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2 flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Judul Tugas <span
                                    class="text-red-500">*</span></label>
                            <input v-model="form.title" type="text" placeholder="Judul tugas..." required
                                class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-indigo-400 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 outline-none transition-all" />
                            <p v-if="form.errors.title" class="text-xs text-red-500">{{ form.errors.title }}</p>
                        </div>

                        <div class="sm:col-span-2 flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Deskripsi</label>
                            <textarea v-model="form.description" rows="3" placeholder="Deskripsi tambahan (opsional)..."
                                class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:border-indigo-400 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 outline-none transition-all font-[inherit] resize-none"></textarea>
                            <p v-if="form.errors.description" class="text-xs text-red-500">{{ form.errors.description }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Kategori</label>
                            <select v-model="form.category_id"
                                class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:border-indigo-400 dark:focus:border-indigo-500 outline-none cursor-pointer">
                                <option value="">Pilih Kategori</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Prioritas</label>
                            <select v-model="form.priority"
                                class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:border-indigo-400 dark:focus:border-indigo-500 outline-none cursor-pointer">
                                <option value="low">🟢 Rendah</option>
                                <option value="medium">🟡 Sedang</option>
                                <option value="high">🟠 Tinggi</option>
                                <option value="urgent">🔴 Urgen</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2 flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Deadline</label>
                            <input v-model="form.deadline" type="datetime-local"
                                class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:border-indigo-400 dark:focus:border-indigo-500 outline-none transition-all" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-slate-100 dark:border-slate-700">
                        <Link :href="route('tasks.index')"
                            class="px-5 py-2.5 bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 rounded-xl text-sm hover:bg-slate-100 dark:hover:bg-slate-600 transition-all">
                            Batal</Link>
                        <button type="submit" :disabled="form.processing"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-200 dark:hover:shadow-indigo-900/50 transition-all disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none">
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
