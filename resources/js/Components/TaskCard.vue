<script setup>
import { Link } from '@inertiajs/vue3';
import { Clock, Pencil, Check } from 'lucide-vue-next';

const props = defineProps({ task: Object });
const emit = defineEmits(['toggle-complete']);

const priorityMap = {
    low: { label: 'Rendah', class: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' },
    medium: { label: 'Sedang', class: 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' },
    high: { label: 'Tinggi', class: 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400' },
    urgent: { label: 'Urgen', class: 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' },
};

const formatDeadline = (deadline) => {
    if (!deadline) return null;
    const date = new Date(deadline);
    const now = new Date();
    const dl = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    const td = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const diff = Math.round((dl - td) / 864e5);

    const text = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    if (diff < 0) return { text, label: `${Math.abs(diff)}d terlewat`, overdue: true };
    if (diff === 0) return { text, label: 'Hari ini', overdue: false };
    if (diff === 1) return { text, label: 'Besok', overdue: false };
    return { text, label: `${diff}d lagi`, overdue: false };
};

const dl = formatDeadline(props.task.deadline);
const prio = priorityMap[props.task.priority] || priorityMap.medium;
</script>

<template>
    <div class="group flex items-start gap-3 px-4 py-3 bg-white dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-800 hover:border-gray-200 dark:hover:border-gray-700 transition-colors"
        :class="{ 'opacity-50': task.status === 'completed' }">

        <button @click="$emit('toggle-complete', task.id)"
            class="mt-0.5 w-[18px] h-[18px] rounded border-[1.5px] flex items-center justify-center flex-shrink-0 transition-colors"
            :class="task.status === 'completed'
                ? 'bg-violet-600 border-violet-600'
                : 'border-gray-300 dark:border-gray-600 hover:border-violet-400 dark:hover:border-violet-500'">
            <Check v-if="task.status === 'completed'" :size="11" color="white" :stroke-width="3" />
        </button>

        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
                <p class="text-sm font-medium leading-snug"
                    :class="task.status === 'completed' ? 'line-through text-gray-400 dark:text-gray-600' : 'text-gray-800 dark:text-gray-100'">
                    {{ task.title }}
                </p>
                <Link :href="route('tasks.edit', task.id)"
                    class="opacity-0 group-hover:opacity-100 p-1 rounded text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-all flex-shrink-0">
                    <Pencil :size="13" />
                </Link>
            </div>

            <p v-if="task.description" class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 line-clamp-1">{{
                task.description }}</p>

            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                <span v-if="task.category" class="px-1.5 py-0.5 text-[10px] font-medium rounded"
                    :style="{ backgroundColor: task.category.color + '12', color: task.category.color }">
                    {{ task.category.name }}
                </span>
                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded" :class="prio.class">
                    {{ prio.label }}
                </span>
                <span v-if="dl" class="flex items-center gap-0.5 text-[11px]"
                    :class="dl.overdue ? 'text-red-500 dark:text-red-400 font-medium' : 'text-gray-400 dark:text-gray-500'">
                    <Clock :size="11" />
                    {{ dl.label }}
                </span>
            </div>
        </div>
    </div>
</template>
