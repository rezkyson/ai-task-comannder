<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AiInput from '@/Components/AiInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Sparkles, FileEdit, ArrowLeft, CheckCircle2, Loader2 } from 'lucide-vue-next';

const props = defineProps({ categories: Array });

const form = useForm({
    title: '', description: '', original_input: '',
    category_id: '', priority: 'medium', deadline: '',
});

const aiParsed = ref(false);

const handleAiParsed = (data) => {
    form.title = data.title || '';
    form.description = data.description || '';
    form.original_input = data.original_input || '';
    form.category_id = data.category_id ? Number(data.category_id) : '';
    form.priority = data.priority || 'medium';
    form.deadline = data.deadline ? fmtDT(data.deadline) : '';
    aiParsed.value = true;
};

const fmtDT = (s) => {
    if (!s) return '';
    const d = new Date(s);
    return new Date(d.getTime() - d.getTimezoneOffset() * 6e4).toISOString().slice(0, 16);
};

const submit = () => {
    if (!form.original_input) form.original_input = form.title;
    form.post(route('tasks.store'), {
        onSuccess: () => { form.reset(); aiParsed.value = false; },
    });
};

const inputClass = 'w-full px-3 py-2 text-sm bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:border-violet-400 dark:focus:border-violet-500 focus:ring-1 focus:ring-violet-100 dark:focus:ring-violet-500/20 outline-none transition-colors';
</script>

<template>

    <Head title="Tambah Tugas" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tambah Tugas</h2>
                <Link :href="route('tasks.index')"
                    class="flex items-center gap-1 text-[13px] text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    <ArrowLeft :size="14" /> Kembali
                </Link>
            </div>
        </template>

        <div class="py-5">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8 space-y-4">

                <!-- AI Section -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <Sparkles :size="15" class="text-violet-500" />
                        <h3 class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">AI Input</h3>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 ml-auto">Opsional</span>
                    </div>
                    <AiInput @parsed="handleAiParsed" />
                </div>

                <!-- AI Success -->
                <div v-if="aiParsed"
                    class="flex items-center gap-2 px-3 py-2 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200/80 dark:border-emerald-500/20 rounded-lg text-emerald-700 dark:text-emerald-400 text-[13px]">
                    <CheckCircle2 :size="15" />
                    <span>Form terisi otomatis. Cek dan simpan.</span>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit"
                    class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-4">
                        <FileEdit :size="15" class="text-blue-500" />
                        <h3 class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Detail Tugas</h3>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[13px] font-medium text-gray-600 dark:text-gray-300 mb-1">Judul
                                <span class="text-red-400">*</span></label>
                            <input v-model="form.title" type="text" :class="inputClass" placeholder="Judul tugas..."
                                required />
                            <p v-if="form.errors.title" class="text-[11px] text-red-500 mt-1">{{ form.errors.title }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-[13px] font-medium text-gray-600 dark:text-gray-300 mb-1">Deskripsi</label>
                            <textarea v-model="form.description" :class="inputClass + ' resize-none'" rows="2"
                                placeholder="Deskripsi (opsional)..."></textarea>
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
                        </div>

                        <div>
                            <label
                                class="block text-[13px] font-medium text-gray-600 dark:text-gray-300 mb-1">Deadline</label>
                            <input v-model="form.deadline" type="datetime-local" :class="inputClass" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <Link :href="route('tasks.index')"
                            class="px-3.5 py-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                            Batal</Link>
                        <button type="submit" :disabled="form.processing"
                            class="flex items-center gap-1.5 px-4 py-2 bg-violet-600 hover:bg-violet-700 disabled:bg-gray-200 dark:disabled:bg-gray-700 text-white disabled:text-gray-400 text-sm font-medium rounded-md transition-colors">
                            <Loader2 v-if="form.processing" :size="14" class="animate-spin" />
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
