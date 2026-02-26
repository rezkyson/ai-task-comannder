<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TaskCard from '@/Components/TaskCard.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Search, Plus, ListFilter, X } from 'lucide-vue-next';

const props = defineProps({
    tasks: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const categoryId = ref(props.filters?.category_id || '');
const priority = ref(props.filters?.priority || '');

let timeout = null;
watch(search, () => { clearTimeout(timeout); timeout = setTimeout(applyFilters, 400); });

function applyFilters() {
    const p = {};
    if (search.value) p.search = search.value;
    if (status.value) p.status = status.value;
    if (categoryId.value) p.category_id = categoryId.value;
    if (priority.value) p.priority = priority.value;
    router.get(route('tasks.index'), p, { preserveState: true, preserveScroll: true });
}

function clearFilters() {
    search.value = ''; status.value = ''; categoryId.value = ''; priority.value = '';
    router.get(route('tasks.index'));
}

const toggleComplete = (id) => {
    const task = props.tasks.data.find(t => t.id === id);
    if (task) {
        const prev = task.status;
        task.status = prev === 'completed' ? 'pending' : 'completed';
        router.patch(route('tasks.toggle-complete', id), {}, {
            preserveScroll: true,
            preserveState: true,
            onError: () => { task.status = prev; },
        });
    }
};
const hasFilters = () => search.value || status.value || categoryId.value || priority.value;
</script>

<template>

    <Head title="Tugas Saya" />
    <AuthenticatedLayout>
        <div class="py-5">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Filters -->
                <div class="flex flex-col sm:flex-row gap-2 mb-5">
                    <div class="relative flex-1">
                        <Search :size="14" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                        <input v-model="search" type="text" placeholder="Cari tugas..."
                            class="w-full pl-8 pr-3 py-2 text-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:border-violet-400 dark:focus:border-violet-500 focus:ring-1 focus:ring-violet-100 dark:focus:ring-violet-500/20 outline-none transition-colors" />
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <select v-model="status" @change="applyFilters"
                            class="px-3 py-2 text-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-gray-600 dark:text-gray-300 focus:border-violet-400 outline-none cursor-pointer">
                            <option value="">Status</option>
                            <option value="pending">Belum Mulai</option>
                            <option value="in_progress">Dikerjakan</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                        <select v-model="categoryId" @change="applyFilters"
                            class="px-3 py-2 text-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-gray-600 dark:text-gray-300 focus:border-violet-400 outline-none cursor-pointer">
                            <option value="">Kategori</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                        <select v-model="priority" @change="applyFilters"
                            class="px-3 py-2 text-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg text-gray-600 dark:text-gray-300 focus:border-violet-400 outline-none cursor-pointer">
                            <option value="">Prioritas</option>
                            <option value="urgent">Urgen</option>
                            <option value="high">Tinggi</option>
                            <option value="medium">Sedang</option>
                            <option value="low">Rendah</option>
                        </select>
                        <button v-if="hasFilters()" @click="clearFilters"
                            class="flex items-center gap-1 px-2.5 py-2 text-sm text-red-500 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors cursor-pointer">
                            <X :size="13" />
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Tasks -->
                <div v-if="tasks.data?.length" class="space-y-2">
                    <TaskCard v-for="task in tasks.data" :key="task.id" :task="task"
                        @toggle-complete="toggleComplete" />
                </div>

                <!-- Empty -->
                <div v-else class="text-center py-16">
                    <div
                        class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-3">
                        <ListFilter :size="20" class="text-gray-300 dark:text-gray-600" />
                    </div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada tugas</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Buat tugas pertamamu dengan AI</p>
                    <Link :href="route('tasks.create')"
                        class="inline-flex items-center gap-1 mt-3 px-3 py-1.5 bg-violet-600 text-white text-sm rounded-md hover:bg-violet-700 transition-colors">
                        <Plus :size="14" /> Buat Tugas
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="tasks.links && tasks.last_page > 1" class="flex justify-center gap-1 mt-6">
                    <template v-for="link in tasks.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url"
                            class="px-2.5 py-1 border rounded text-xs transition-colors"
                            :class="link.active ? 'bg-violet-600 text-white border-violet-600' : 'border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'"
                            v-html="link.label" />
                        <span v-else
                            class="px-2.5 py-1 border border-gray-100 dark:border-gray-800 rounded text-xs text-gray-300 dark:text-gray-600"
                            v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
