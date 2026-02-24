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

const deleteTask = (taskId) => {
    if (confirm('Yakin ingin menghapus task ini?')) {
        router.delete(route('tasks.destroy', taskId), {
            preserveScroll: true,
        });
    }
};

const hasActiveFilters = () => {
    return search.value || status.value || categoryId.value || priority.value;
};
</script>

<template>
    <Head title="Tugas Saya" />

    <AuthenticatedLayout>
        <template #header>
            <div class="header-row">
                <h2 class="page-title">Tugas Saya</h2>
                <Link :href="route('tasks.create')" class="create-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M8 12h8"/>
                        <path d="M12 8v8"/>
                    </svg>
                    Tambah Tugas
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="filters-bar">
                    <div class="search-box">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                        <input v-model="search" type="text" placeholder="Cari tugas..." class="search-input" />
                    </div>

                    <select v-model="status" @change="applyFilters" class="filter-select">
                        <option value="">Semua Status</option>
                        <option value="pending">Belum Mulai</option>
                        <option value="in_progress">Sedang Dikerjakan</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>

                    <select v-model="categoryId" @change="applyFilters" class="filter-select">
                        <option value="">Semua Kategori</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>

                    <select v-model="priority" @change="applyFilters" class="filter-select">
                        <option value="">Semua Prioritas</option>
                        <option value="urgent">Urgen</option>
                        <option value="high">Tinggi</option>
                        <option value="medium">Sedang</option>
                        <option value="low">Rendah</option>
                    </select>

                    <button v-if="hasActiveFilters()" @click="clearFilters" class="clear-filters-btn">
                        Reset
                    </button>
                </div>

                <!-- Task List -->
                <div v-if="tasks.data && tasks.data.length > 0" class="task-list">
                    <TaskCard
                        v-for="task in tasks.data"
                        :key="task.id"
                        :task="task"
                        @toggle-complete="toggleComplete"
                    />
                </div>

                <!-- Empty State -->
                <div v-else class="empty-state">
                    <div class="empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-16 h-16">
                            <path d="M12 2a4 4 0 0 1 4 4v1a3 3 0 0 1 3 3v1a2 2 0 0 1-2 2h-1v4a4 4 0 0 1-8 0v-4H7a2 2 0 0 1-2-2v-1a3 3 0 0 1 3-3V6a4 4 0 0 1 4-4z"/>
                            <circle cx="9" cy="13" r="1"/>
                            <circle cx="15" cy="13" r="1"/>
                        </svg>
                    </div>
                    <h3 class="empty-title">Belum ada tugas</h3>
                    <p class="empty-text">Mulai buat tugas pertamamu dengan AI! Cukup ketik dengan bahasa sehari-hari.</p>
                    <Link :href="route('tasks.create')" class="create-btn" style="margin-top: 1rem;">
                        Buat Tugas Pertama
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="tasks.links && tasks.last_page > 1" class="pagination">
                    <template v-for="link in tasks.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="page-link"
                            :class="{ active: link.active }"
                            v-html="link.label"
                        />
                        <span v-else class="page-link disabled" v-html="link.label" />
                    </template>
                </div>
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

.create-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 0.75rem;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.create-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
}

.filters-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    align-items: center;
}

.search-box {
    position: relative;
    flex: 1;
    min-width: 200px;
}

.search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    width: 1.1rem;
    height: 1.1rem;
    color: #94a3b8;
}

.search-input {
    width: 100%;
    padding: 0.5rem 0.75rem 0.5rem 2.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.625rem;
    font-size: 0.875rem;
    outline: none;
    transition: all 0.2s ease;
    background: white;
}

.search-input:focus {
    border-color: #818cf8;
    box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.15);
}

.filter-select {
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.625rem;
    font-size: 0.85rem;
    outline: none;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #475569;
}

.filter-select:focus {
    border-color: #818cf8;
    box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.15);
}

.clear-filters-btn {
    padding: 0.5rem 0.875rem;
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
    border-radius: 0.625rem;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.clear-filters-btn:hover {
    background: #fecaca;
}

.task-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
    color: #c7d2fe;
}

.empty-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.5rem;
}

.empty-text {
    font-size: 0.875rem;
    color: #94a3b8;
    max-width: 320px;
    margin: 0 auto;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.25rem;
    margin-top: 2rem;
}

.page-link {
    padding: 0.375rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 0.8rem;
    color: #475569;
    text-decoration: none;
    transition: all 0.2s ease;
}

.page-link:hover:not(.disabled):not(.active) {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.page-link.active {
    background: #6366f1;
    color: white;
    border-color: #6366f1;
}

.page-link.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

@media (max-width: 640px) {
    .header-row {
        flex-direction: column;
        gap: 0.75rem;
        align-items: flex-start;
    }

    .filters-bar {
        flex-direction: column;
    }

    .search-box {
        width: 100%;
    }

    .filter-select {
        width: 100%;
    }
}
</style>
