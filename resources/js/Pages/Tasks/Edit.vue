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
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Edit Tugas</h2>
                <Link :href="route('tasks.index')"
                    class="text-sm font-medium text-indigo-500 hover:text-indigo-700 dark:hover:text-indigo-400">←
                    Kembali</Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <!-- Original Input -->
                <div v-if="task.original_input"
                    class="bg-gradient-to-br from-blue-50 to-violet-50 dark:from-indigo-500/10 dark:to-purple-500/10 border border-indigo-200 dark:border-indigo-500/30 rounded-xl px-5 py-4 mb-5">
                    <div
                        class="text-xs font-semibold text-indigo-500 dark:text-indigo-400 uppercase tracking-wider mb-1">
                        Input
                        asli dari AI:</div>
                    <div class="text-sm text-indigo-700 dark:text-indigo-300 italic">"{{ task.original_input }}"</div>
                </div>

                <!-- Edit Form -->
                <form @submit.prevent="submit"
                    class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2 flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Judul Tugas <span
                                    class="text-red-500">*</span></label>
                            <input v-model="form.title" type="text" required
                                class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:border-indigo-400 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 outline-none transition-all" />
                            <p v-if="form.errors.title" class="text-xs text-red-500">{{ form.errors.title }}</p>
                        </div>

                        <div class="sm:col-span-2 flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Deskripsi</label>
                            <textarea v-model="form.description" rows="3"
                                class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:border-indigo-400 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 outline-none transition-all font-[inherit] resize-none"></textarea>
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

                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
                            <select v-model="form.status"
                                class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:border-indigo-400 dark:focus:border-indigo-500 outline-none cursor-pointer">
                                <option value="pending">Belum Mulai</option>
                                <option value="in_progress">Sedang Dikerjakan</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Deadline</label>
                            <input v-model="form.deadline" type="datetime-local"
                                class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 focus:border-indigo-400 dark:focus:border-indigo-500 outline-none transition-all" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex flex-col sm:flex-row justify-between items-center gap-3 mt-6 pt-5 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="deleteTask"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-white dark:bg-slate-700 text-red-500 border border-red-200 dark:border-red-500/30 rounded-xl text-sm cursor-pointer hover:bg-red-50 dark:hover:bg-red-500/10 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="w-4 h-4">
                                <path d="M3 6h18" />
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                            </svg>
                            Hapus
                        </button>
                        <div class="flex gap-3">
                            <Link :href="route('tasks.index')"
                                class="px-5 py-2.5 bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 rounded-xl text-sm hover:bg-slate-100 dark:hover:bg-slate-600 transition-all">
                                Batal</Link>
                            <button type="submit" :disabled="form.processing"
                                class="px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-200 dark:hover:shadow-indigo-900/50 transition-all disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none">
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
