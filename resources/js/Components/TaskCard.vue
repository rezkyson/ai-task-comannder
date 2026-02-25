<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    task: Object,
});

const emit = defineEmits(['toggle-complete']);

const priorityColors = {
    low: { bg: 'bg-green-50 dark:bg-green-500/10', text: 'text-green-600 dark:text-green-400', border: 'border-green-200 dark:border-green-500/30' },
    medium: { bg: 'bg-amber-50 dark:bg-amber-500/10', text: 'text-amber-600 dark:text-amber-400', border: 'border-amber-200 dark:border-amber-500/30' },
    high: { bg: 'bg-red-50 dark:bg-red-500/10', text: 'text-red-600 dark:text-red-400', border: 'border-red-200 dark:border-red-500/30' },
    urgent: { bg: 'bg-red-100 dark:bg-red-500/15', text: 'text-red-700 dark:text-red-400', border: 'border-red-300 dark:border-red-500/40' },
};

const priorityLabels = {
    low: 'Rendah', medium: 'Sedang', high: 'Tinggi', urgent: 'Urgen',
};

const formatDeadline = (deadline) => {
    if (!deadline) return null;
    const date = new Date(deadline);
    const now = new Date();
    const deadlineDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const todayDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const diffDays = Math.round((deadlineDate - todayDate) / (1000 * 60 * 60 * 24));

    const formatted = date.toLocaleDateString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
    });

    if (diffDays < 0) return { text: formatted, label: `${Math.abs(diffDays)} hari terlewat`, isOverdue: true };
    if (diffDays === 0) return { text: formatted, label: 'Hari ini', isOverdue: false };
    if (diffDays === 1) return { text: formatted, label: 'Besok', isOverdue: false };
    return { text: formatted, label: `${diffDays} hari lagi`, isOverdue: false };
};

const deadline = formatDeadline(props.task.deadline);
</script>

<template>
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3.5 hover:border-indigo-200 dark:hover:border-indigo-500/30 hover:shadow-sm transition-all"
        :class="{ 'opacity-60': task.status === 'completed' }">
        <div class="flex gap-3.5 items-start">
            <!-- Checkbox -->
            <button @click="$emit('toggle-complete', task.id)"
                class="flex-shrink-0 w-5 h-5 mt-0.5 rounded-md border-2 flex items-center justify-center cursor-pointer transition-all"
                :class="task.status === 'completed'
                    ? 'bg-indigo-500 border-indigo-500 text-white'
                    : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 hover:border-indigo-400'">
                <svg v-if="task.status === 'completed'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                    class="w-3 h-3">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
            </button>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start gap-2">
                    <h3 class="text-[0.95rem] font-semibold leading-snug" :class="task.status === 'completed'
                        ? 'line-through text-slate-400 dark:text-slate-500'
                        : 'text-slate-800 dark:text-slate-100'">
                        {{ task.title }}
                    </h3>
                    <Link :href="route('tasks.edit', task.id)"
                        class="flex-shrink-0 p-1 rounded-md text-slate-400 dark:text-slate-500 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />
                            <path d="m15 5 4 4" />
                        </svg>
                    </Link>
                </div>

                <p v-if="task.description" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">{{
                    task.description }}</p>

                <div class="flex flex-wrap gap-2 mt-2 items-center">
                    <span v-if="task.category"
                        class="inline-flex items-center px-2 py-0.5 text-[0.7rem] font-semibold rounded-full border"
                        :style="{ backgroundColor: task.category.color + '15', color: task.category.color, borderColor: task.category.color + '30' }">
                        {{ task.category.name }}
                    </span>

                    <span class="inline-flex items-center px-2 py-0.5 text-[0.7rem] font-semibold rounded-full border"
                        :class="[priorityColors[task.priority].bg, priorityColors[task.priority].text, priorityColors[task.priority].border]">
                        {{ priorityLabels[task.priority] }}
                    </span>

                    <span v-if="deadline" class="inline-flex items-center gap-1 text-xs"
                        :class="deadline.isOverdue ? 'text-red-500 dark:text-red-400 font-semibold' : 'text-slate-500 dark:text-slate-400'">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        {{ deadline.label }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
