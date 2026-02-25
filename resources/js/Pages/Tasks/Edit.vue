<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ArrowLeft, Trash2, Loader2, Sparkles } from 'lucide-vue-next';

const props = defineProps({ task: Object, categories: Array });

const fmtDT = (s) => {
    if (!s) return '';
    const d = new Date(s);
    return new Date(d.getTime() - d.getTimezoneOffset() * 6e4).toISOString().slice(0, 16);
};

const form = useForm({
    title: props.task.title || '',
    description: props.task.description || '',
    category_id: props.task.category_id || '',
    priority: props.task.priority || 'medium',
    status: props.task.status || 'pending',
    deadline: fmtDT(props.task.deadline),
});

const submit = () => form.put(route('tasks.update', props.task.id));
const deleteTask = () => { if (confirm('Yakin ingin menghapus?')) router.delete(route('tasks.destroy', props.task.id)); };

const inputClass = 'w-full px-3 py-2 text-sm bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:border-violet-400 dark:focus:border-violet-500 focus:ring-1 focus:ring-violet-100 dark:focus:ring-violet-500/20 outline-none transition-colors';
</script>

<template>

    <Head :title="'Edit: ' + task.title" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Tugas</h2>
                <Link :href="route('tasks.index')"
                    class="flex items-center gap-1 text-[13px] text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    <ArrowLeft :size="14" /> Kembali
                </Link>
            </div>
        </template>

        <div class="py-5">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 space-y-4">

                <!-- Original Input -->
                <div v-if="task.original_input"
                    class="flex items-start gap-2 px-3.5 py-2.5 bg-violet-50/50 dark:bg-violet-500/5 border border-violet-100 dark:border-violet-500/15 rounded-lg">
                    <Sparkles :size="14" class="text-violet-500 mt-0.5 flex-shrink-0" />
                    <div>
                        <p
                            class="text-[10px] font-semibold text-violet-500 dark:text-violet-400 uppercase tracking-wider">
                            Input
                            AI</p>
                        <p class="text-[13px] text-violet-700 dark:text-violet-300 italic mt-0.5">"{{
                            task.original_input }}"
                        </p>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit"
                    class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg p-4">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-[13px] font-medium text-gray-600 dark:text-gray-300 mb-1">Judul
                                <span class="text-red-400">*</span></label>
                            <input v-model="form.title" type="text" :class="inputClass" required />
                            <p v-if="form.errors.title" class="text-[11px] text-red-500 mt-1">{{ form.errors.title }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-[13px] font-medium text-gray-600 dark:text-gray-300 mb-1">Deskripsi</label>
                            <textarea v-model="form.description" :class="inputClass + ' resize-none'"
                                rows="2"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block text-[13px] font-medium text-gray-600 dark:text-gray-300 mb-1">Kategori</label>
                                <select v-model="form.category_id" :class="inputClass + ' cursor-pointer'">
                                    <option value="">Pilih...</option>
                                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-[13px] font-medium text-gray-600 dark:text-gray-300 mb-1">Prioritas</label>
                                <select v-model="form.priority" :class="inputClass + ' cursor-pointer'">
                                    <option value="low">Rendah</option>
                                    <option value="medium">Sedang</option>
                                    <option value="high">Tinggi</option>
                                    <option value="urgent">Urgen</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-[13px] font-medium text-gray-600 dark:text-gray-300 mb-1">Status</label>
                                <select v-model="form.status" :class="inputClass + ' cursor-pointer'">
                                    <option value="pending">Belum Mulai</option>
                                    <option value="in_progress">Dikerjakan</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-[13px] font-medium text-gray-600 dark:text-gray-300 mb-1">Deadline</label>
                                <input v-model="form.deadline" type="datetime-local" :class="inputClass" />
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" @click="deleteTask"
                            class="flex items-center gap-1 px-3 py-1.5 text-[13px] text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-md transition-colors cursor-pointer">
                            <Trash2 :size="14" /> Hapus
                        </button>
                        <div class="flex gap-2">
                            <Link :href="route('tasks.index')"
                                class="px-3.5 py-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                                Batal</Link>
                            <button type="submit" :disabled="form.processing"
                                class="flex items-center gap-1.5 px-4 py-2 bg-violet-600 hover:bg-violet-700 disabled:bg-gray-200 dark:disabled:bg-gray-700 text-white disabled:text-gray-400 text-sm font-medium rounded-md transition-colors">
                                <Loader2 v-if="form.processing" :size="14" class="animate-spin" />
                                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
