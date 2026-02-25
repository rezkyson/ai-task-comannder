<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    upcomingTasks: Array,
    tasksByCategory: Array,
    recentTasks: Array,
});

const userName = computed(() => usePage().props.auth.user.name);

const completionRate = computed(() => {
    if (props.stats.total === 0) return 0;
    return Math.round((props.stats.completed / props.stats.total) * 100);
});

const formatRelativeDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    const now = new Date();
    const deadlineDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const todayDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const diffDays = Math.round((deadlineDate - todayDate) / (1000 * 60 * 60 * 24));

    if (diffDays < 0) return `${Math.abs(diffDays)} hari terlewat`;
    if (diffDays === 0) return 'Hari ini';
    if (diffDays === 1) return 'Besok';
    return `${diffDays} hari lagi`;
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit',
    });
};

const maxCategoryCount = computed(() => {
    if (!props.tasksByCategory || props.tasksByCategory.length === 0) return 1;
    return Math.max(...props.tasksByCategory.map(c => c.count));
});

const priorityLabels = {
    low: 'Rendah', medium: 'Sedang', high: 'Tinggi', urgent: 'Urgen',
};

const priorityColors = {
    low: '#16a34a', medium: '#d97706', high: '#dc2626', urgent: '#991b1b',
};
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Dashboard</h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Greeting -->
                <div class="mb-6">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                        Halo, <span
                            class="bg-gradient-to-r from-indigo-500 to-purple-500 bg-clip-text text-transparent">{{
                            userName }}</span>! 👋
                    </h1>
                    <p class="text-gray-500 dark:text-slate-400 text-sm mt-1">Berikut ringkasan tugas kamu hari ini.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
                    <div
                        class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-4 flex items-center gap-3.5 hover:border-indigo-200 dark:hover:border-indigo-500/30 hover:shadow-sm transition-all">
                        <div
                            class="w-11 h-11 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#3b82f6"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-extrabold text-gray-800 dark:text-white leading-tight">{{
                                stats.total
                                }}</span>
                            <span class="text-xs text-gray-400 dark:text-slate-500 font-medium">Total Tugas</span>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-4 flex items-center gap-3.5 hover:border-indigo-200 dark:hover:border-indigo-500/30 hover:shadow-sm transition-all">
                        <div
                            class="w-11 h-11 rounded-lg bg-green-50 dark:bg-green-500/10 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#16a34a"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-extrabold text-gray-800 dark:text-white leading-tight">{{
                                stats.completed
                                }}</span>
                            <span class="text-xs text-gray-400 dark:text-slate-500 font-medium">Selesai</span>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-4 flex items-center gap-3.5 hover:border-indigo-200 dark:hover:border-indigo-500/30 hover:shadow-sm transition-all">
                        <div
                            class="w-11 h-11 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#d97706"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-extrabold text-gray-800 dark:text-white leading-tight">{{
                                stats.pending
                                }}</span>
                            <span class="text-xs text-gray-400 dark:text-slate-500 font-medium">Belum Mulai</span>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-4 flex items-center gap-3.5 hover:border-indigo-200 dark:hover:border-indigo-500/30 hover:shadow-sm transition-all">
                        <div
                            class="w-11 h-11 rounded-lg bg-red-50 dark:bg-red-500/10 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#dc2626"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                                <path
                                    d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                                <line x1="12" y1="9" x2="12" y2="13" />
                                <line x1="12" y1="17" x2="12.01" y2="17" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-2xl font-extrabold text-gray-800 dark:text-white leading-tight">{{
                                stats.overdue
                                }}</span>
                            <span class="text-xs text-gray-400 dark:text-slate-500 font-medium">Terlewat</span>
                        </div>
                    </div>
                </div>

                <!-- Completion Progress -->
                <div v-if="stats.total > 0"
                    class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl px-5 py-4 mb-5">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-semibold text-gray-700 dark:text-slate-200">Progress
                            Keseluruhan</span>
                        <span class="text-sm font-bold text-indigo-500">{{ completionRate }}%</span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-500"
                            :style="{ width: completionRate + '%' }"></div>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <!-- Upcoming Tasks -->
                    <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-gray-800 dark:text-white">⏰ Deadline Terdekat</h3>
                            <Link :href="route('tasks.index')"
                                class="text-xs text-indigo-500 font-medium hover:text-indigo-700 dark:hover:text-indigo-400">
                                Lihat Semua →</Link>
                        </div>
                        <div v-if="upcomingTasks && upcomingTasks.length > 0" class="flex flex-col gap-3">
                            <div v-for="task in upcomingTasks" :key="task.id"
                                class="flex justify-between items-center py-2.5 border-b border-gray-50 dark:border-slate-700/50 last:border-b-0 last:pb-0">
                                <div class="flex-1 min-w-0">
                                    <Link :href="route('tasks.edit', task.id)"
                                        class="text-sm font-semibold text-gray-800 dark:text-slate-200 hover:text-indigo-500 dark:hover:text-indigo-400 block truncate">
                                        {{ task.title }}
                                    </Link>
                                    <div class="flex gap-2 text-xs mt-0.5">
                                        <span v-if="task.category" class="font-semibold"
                                            :style="{ color: task.category.color }">
                                            {{ task.category.name }}
                                        </span>
                                        <span class="font-semibold" :style="{ color: priorityColors[task.priority] }">
                                            {{ priorityLabels[task.priority] }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 ml-4">
                                    <span class="block text-xs font-semibold text-amber-600 dark:text-amber-400">{{
                                        formatRelativeDate(task.deadline) }}</span>
                                    <span class="block text-xs text-gray-400 dark:text-slate-500">{{
                                        formatDate(task.deadline)
                                        }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-6 text-gray-400 dark:text-slate-500 text-sm">
                            <p>Tidak ada deadline mendatang 🎉</p>
                        </div>
                    </div>

                    <!-- Tasks by Category -->
                    <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-gray-800 dark:text-white">📊 Tugas per Kategori</h3>
                        </div>
                        <div v-if="tasksByCategory && tasksByCategory.length > 0" class="flex flex-col gap-3">
                            <div v-for="cat in tasksByCategory" :key="cat.name" class="flex items-center gap-3">
                                <div class="flex items-center gap-2 w-24 flex-shrink-0">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                                        :style="{ backgroundColor: cat.color }"></span>
                                    <span class="text-xs text-gray-500 dark:text-slate-400 font-medium truncate">{{
                                        cat.name
                                        }}</span>
                                </div>
                                <div class="flex-1 flex items-center gap-2">
                                    <div class="h-5 rounded-md min-w-[4px] transition-all duration-300" :style="{
                                        width: (cat.count / maxCategoryCount * 100) + '%',
                                        backgroundColor: cat.color
                                    }"></div>
                                    <span class="text-xs font-bold text-gray-700 dark:text-slate-300 min-w-[1.5rem]">{{
                                        cat.count }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-6 text-gray-400 dark:text-slate-500 text-sm">
                            <p>Belum ada tugas dengan kategori</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Tasks -->
                <div
                    class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-5 mt-5">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white">🕐 Tugas Terbaru</h3>
                        <Link :href="route('tasks.create')"
                            class="text-xs text-indigo-500 font-medium hover:text-indigo-700 dark:hover:text-indigo-400">
                            +
                            Tambah Tugas</Link>
                    </div>
                    <div v-if="recentTasks && recentTasks.length > 0" class="flex flex-col">
                        <div v-for="task in recentTasks" :key="task.id"
                            class="flex items-center gap-3 py-2.5 border-b border-gray-50 dark:border-slate-700/50 last:border-b-0 last:pb-0">
                            <span class="w-2 h-2 rounded-full flex-shrink-0" :class="{
                                'bg-yellow-400': task.status === 'pending',
                                'bg-blue-500': task.status === 'in_progress',
                                'bg-green-500': task.status === 'completed',
                                'bg-gray-400': task.status === 'cancelled',
                            }"></span>
                            <div class="flex-1 min-w-0">
                                <Link :href="route('tasks.edit', task.id)"
                                    class="text-sm font-semibold text-gray-800 dark:text-slate-200 hover:text-indigo-500 dark:hover:text-indigo-400 block truncate">
                                    {{ task.title }}
                                </Link>
                                <span class="text-xs text-gray-400 dark:text-slate-500">Dibuat {{
                                    formatDate(task.created_at)
                                    }}</span>
                            </div>
                            <span v-if="task.category" class="text-xs font-semibold flex-shrink-0"
                                :style="{ color: task.category.color }">
                                {{ task.category.name }}
                            </span>
                        </div>
                    </div>
                    <div v-else class="text-center py-6 text-gray-400 dark:text-slate-500 text-sm">
                        <p>Belum ada tugas.
                            <Link :href="route('tasks.create')" class="text-indigo-500 font-semibold">Buat sekarang!
                            </Link>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
