<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ClipboardList, CheckCircle2, Clock, AlertTriangle, ArrowRight, TrendingUp, CalendarClock, BarChart3, History, CircleDot, Loader, CircleCheck, Ban, Plus } from 'lucide-vue-next';

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

const formatRelative = (dateStr) => {
    if (!dateStr) return '';
    const d = new Date(dateStr), n = new Date();
    const dl = new Date(d.getFullYear(), d.getMonth(), d.getDate());
    const td = new Date(n.getFullYear(), n.getMonth(), n.getDate());
    const diff = Math.round((dl - td) / 864e5);
    if (diff < 0) return `${Math.abs(diff)}d terlewat`;
    if (diff === 0) return 'Hari ini';
    if (diff === 1) return 'Besok';
    return `${diff}d lagi`;
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
};

const maxCatCount = computed(() => {
    if (!props.tasksByCategory?.length) return 1;
    return Math.max(...props.tasksByCategory.map(c => c.count));
});

const statCards = computed(() => [
    { label: 'Total', value: props.stats.total, icon: ClipboardList, color: 'text-violet-600 dark:text-violet-400', bg: 'bg-violet-50 dark:bg-violet-500/10' },
    { label: 'Selesai', value: props.stats.completed, icon: CheckCircle2, color: 'text-emerald-600 dark:text-emerald-400', bg: 'bg-emerald-50 dark:bg-emerald-500/10' },
    { label: 'Pending', value: props.stats.pending, icon: Clock, color: 'text-amber-600 dark:text-amber-400', bg: 'bg-amber-50 dark:bg-amber-500/10' },
    { label: 'Terlewat', value: props.stats.overdue, icon: AlertTriangle, color: 'text-red-600 dark:text-red-400', bg: 'bg-red-50 dark:bg-red-500/10' },
]);

const statusMap = {
    pending: { label: 'Pending', icon: CircleDot, class: 'text-amber-500 dark:text-amber-400' },
    in_progress: { label: 'Dikerjakan', icon: Loader, class: 'text-blue-500 dark:text-blue-400' },
    completed: { label: 'Selesai', icon: CircleCheck, class: 'text-emerald-500 dark:text-emerald-400' },
    cancelled: { label: 'Dibatalkan', icon: Ban, class: 'text-gray-400 dark:text-gray-500' },
};

const priorityColors = { low: '#16a34a', medium: '#d97706', high: '#ea580c', urgent: '#dc2626' };
const priorityLabels = { low: 'Rendah', medium: 'Sedang', high: 'Tinggi', urgent: 'Urgen' };
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="py-5">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-5">

                <!-- Greeting -->
                <div>
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Selamat datang, {{ userName }}
                    </h1>
                    <p class="text-[13px] text-gray-400 dark:text-gray-500 mt-0.5">Ringkasan aktivitas tugas kamu</p>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div v-for="s in statCards" :key="s.label"
                        class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg p-3.5 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-md flex items-center justify-center flex-shrink-0" :class="s.bg">
                            <component :is="s.icon" :size="18" :class="s.color" />
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-900 dark:text-white leading-none">{{ s.value }}</p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ s.label }}</p>
                        </div>
                    </div>
                </div>

                <!-- Progress -->
                <div v-if="stats.total > 0" class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg px-4 py-3">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5 text-[13px] font-medium text-gray-600 dark:text-gray-300">
                            <TrendingUp :size="14" />
                            Progress
                        </div>
                        <span class="text-[13px] font-semibold text-violet-600 dark:text-violet-400">{{ completionRate }}%</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-violet-600 rounded-full transition-all duration-500" :style="{ width: completionRate + '%' }"></div>
                    </div>
                </div>

                <!-- Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                    <!-- Upcoming -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50 dark:border-gray-800">
                            <div class="flex items-center gap-1.5">
                                <CalendarClock :size="14" class="text-amber-500" />
                                <h3 class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Deadline Terdekat</h3>
                            </div>
                            <Link :href="route('tasks.index')" class="text-[11px] text-violet-600 dark:text-violet-400 font-medium flex items-center gap-0.5 hover:gap-1 transition-all">
                                Semua <ArrowRight :size="11" />
                            </Link>
                        </div>
                        <div v-if="upcomingTasks?.length" class="divide-y divide-gray-50 dark:divide-gray-800">
                            <Link v-for="t in upcomingTasks" :key="t.id" :href="route('tasks.edit', t.id)"
                                class="flex items-center justify-between px-4 py-2.5 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 truncate">{{ t.title }}</p>
                                    <div class="flex gap-2 mt-0.5">
                                        <span v-if="t.category" class="text-[10px] font-medium" :style="{ color: t.category.color }">{{ t.category.name }}</span>
                                        <span class="text-[10px] font-medium" :style="{ color: priorityColors[t.priority] }">{{ priorityLabels[t.priority] }}</span>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 ml-3">
                                    <span class="text-[11px] font-medium text-amber-600 dark:text-amber-400">{{ formatRelative(t.deadline) }}</span>
                                    <span class="block text-[10px] text-gray-400">{{ formatDate(t.deadline) }}</span>
                                </div>
                            </Link>
                        </div>
                        <p v-else class="text-center py-8 text-sm text-gray-400 dark:text-gray-500">Tidak ada deadline mendatang</p>
                    </div>

                    <!-- Categories -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg">
                        <div class="px-4 py-3 border-b border-gray-50 dark:border-gray-800">
                            <div class="flex items-center gap-1.5">
                                <BarChart3 :size="14" class="text-blue-500" />
                                <h3 class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Tugas per Kategori</h3>
                            </div>
                        </div>
                        <div v-if="tasksByCategory?.length" class="px-4 py-3 space-y-2.5">
                            <div v-for="cat in tasksByCategory" :key="cat.name" class="flex items-center gap-2.5">
                                <div class="flex items-center gap-1.5 w-20 flex-shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: cat.color }"></span>
                                    <span class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ cat.name }}</span>
                                </div>
                                <div class="flex-1 flex items-center gap-2">
                                    <div class="h-4 rounded min-w-[3px] transition-all duration-300"
                                        :style="{ width: (cat.count / maxCatCount * 100) + '%', backgroundColor: cat.color, opacity: 0.7 }"></div>
                                    <span class="text-[11px] font-semibold text-gray-600 dark:text-gray-300">{{ cat.count }}</span>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-center py-8 text-sm text-gray-400 dark:text-gray-500">Belum ada kategori</p>
                    </div>
                </div>

                <!-- Recent -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50 dark:border-gray-800">
                        <div class="flex items-center gap-1.5">
                            <History :size="14" class="text-violet-500" />
                            <h3 class="text-[13px] font-semibold text-gray-800 dark:text-gray-200">Terbaru</h3>
                        </div>
                        <Link :href="route('tasks.create')" class="flex items-center gap-0.5 text-[11px] text-violet-600 dark:text-violet-400 font-medium">
                            <Plus :size="11" /> Tambah
                        </Link>
                    </div>
                    <div v-if="recentTasks?.length" class="divide-y divide-gray-50 dark:divide-gray-800">
                        <Link v-for="t in recentTasks" :key="t.id" :href="route('tasks.edit', t.id)"
                            class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                            <component :is="statusMap[t.status]?.icon || CircleDot" :size="14" :class="statusMap[t.status]?.class" class="flex-shrink-0" />
                            <p class="text-sm text-gray-800 dark:text-gray-200 truncate flex-1">{{ t.title }}</p>
                            <span class="text-[10px] font-medium flex-shrink-0" :class="statusMap[t.status]?.class">
                                {{ statusMap[t.status]?.label }}
                            </span>
                            <span v-if="t.category" class="text-[10px] font-medium flex-shrink-0 hidden sm:inline" :style="{ color: t.category.color }">{{ t.category.name }}</span>
                        </Link>
                    </div>
                    <div v-else class="text-center py-8">
                        <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada tugas</p>
                        <Link :href="route('tasks.create')" class="text-sm text-violet-600 dark:text-violet-400 font-medium mt-1 inline-block">Buat sekarang</Link>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
