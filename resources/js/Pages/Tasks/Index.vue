<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TaskCard from '@/Components/TaskCard.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    tasks: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const categoryId = ref(props.filters?.category_id || '');
const priority = ref(props.filters?.priority || '');

let searchTimeout = null;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 400);
});

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (status.value) params.status = status.value;
    if (categoryId.value) params.category_id = categoryId.value;
    if (priority.value) params.priority = priority.value;

    router.get(route('tasks.index'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    status.value = '';
    categoryId.value = '';
    priority.value = '';
    router.get(route('tasks.index'));
};

const toggleComplete = (taskId) => {
    router.patch(route('tasks.toggle-complete', taskId), {}, {
        preserveScroll: true,
    });
};

const hasActiveFilters = () => {
    return search.value || status.value || categoryId.value || priority.value;
};
</script>

<template>

    <Head title="Tugas Saya" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white">Tugas Saya</h2>
                <Link :href="route('tasks.create')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-200 dark:hover:shadow-indigo-900/50 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M8 12h8" />
                        <path d="M12 8v8" />
                    </svg>
                    Tambah Tugas
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row flex-wrap gap-3 mb-6">
                    <div class="relative flex-1 min-w-[200px]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                        <input v-model="search" type="text" placeholder="Cari tugas..."
                            class="w-full pl-10 pr-3 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:border-indigo-400 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-500/20 outline-none transition-all" />
                    </div>
                    <select v-model="status" @change="applyFilters"
                        class="px-3 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 focus:border-indigo-400 dark:focus:border-indigo-500 outline-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="pending">Belum Mulai</option>
                        <option value="in_progress">Sedang Dikerjakan</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                    <select v-model="categoryId" @change="applyFilters"
                        class="px-3 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 focus:border-indigo-400 dark:focus:border-indigo-500 outline-none cursor-pointer">
                        <option value="">Semua Kategori</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <select v-model="priority" @change="applyFilters"
                        class="px-3 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl text-sm bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 focus:border-indigo-400 dark:focus:border-indigo-500 outline-none cursor-pointer">
                        <option value="">Semua Prioritas</option>
                        <option value="urgent">Urgen</option>
                        <option value="high">Tinggi</option>
                        <option value="medium">Sedang</option>
                        <option value="low">Rendah</option>
                    </select>
                    <button v-if="hasActiveFilters()" @click="clearFilters"
                        class="px-3.5 py-2.5 bg-red-50 dark:bg-red-500/10 text-red-500 border border-red-200 dark:border-red-500/30 rounded-xl text-sm font-medium hover:bg-red-100 dark:hover:bg-red-500/20 cursor-pointer transition-all">
                        Reset
                    </button>
                </div>

                <!-- Task List -->
                <div v-if="tasks.data && tasks.data.length > 0" class="flex flex-col gap-3">
                    <TaskCard v-for="task in tasks.data" :key="task.id" :task="task"
                        @toggle-complete="toggleComplete" />
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-12">
                    <div class="flex justify-center mb-4 text-indigo-200 dark:text-indigo-800">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-16 h-16">
                            <path
                                d="M12 2a4 4 0 0 1 4 4v1a3 3 0 0 1 3 3v1a2 2 0 0 1-2 2h-1v4a4 4 0 0 1-8 0v-4H7a2 2 0 0 1-2-2v-1a3 3 0 0 1 3-3V6a4 4 0 0 1 4-4z" />
                            <circle cx="9" cy="13" r="1" />
                            <circle cx="15" cy="13" r="1" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-500 dark:text-slate-400 mb-1">Belum ada tugas</h3>
                    <p class="text-sm text-slate-400 dark:text-slate-500 max-w-xs mx-auto">Mulai buat tugas pertamamu
                        dengan AI!
                    </p>
                    <Link :href="route('tasks.create')"
                        class="inline-flex mt-4 px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                        Buat Tugas Pertama
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="tasks.links && tasks.last_page > 1" class="flex justify-center gap-1 mt-8">
                    <template v-for="link in tasks.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url"
                            class="px-3 py-1.5 border rounded-lg text-xs transition-all"
                            :class="link.active
                                ? 'bg-indigo-500 text-white border-indigo-500'
                                : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'" v-html="link.label" />
                        <span v-else
                            class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-300 dark:text-slate-600"
                            v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
