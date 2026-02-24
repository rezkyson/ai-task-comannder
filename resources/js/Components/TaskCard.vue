<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    task: Object,
});

const emit = defineEmits(['toggle-complete']);

const priorityColors = {
    low: { bg: '#f0fdf4', text: '#16a34a', border: '#bbf7d0' },
    medium: { bg: '#fffbeb', text: '#d97706', border: '#fde68a' },
    high: { bg: '#fef2f2', text: '#dc2626', border: '#fecaca' },
    urgent: { bg: '#fef2f2', text: '#991b1b', border: '#fca5a5' },
};

const priorityLabels = {
    low: 'Rendah',
    medium: 'Sedang',
    high: 'Tinggi',
    urgent: 'Urgen',
};

const statusLabels = {
    pending: 'Belum Mulai',
    in_progress: 'Sedang Dikerjakan',
    completed: 'Selesai',
    cancelled: 'Dibatalkan',
};

const formatDeadline = (deadline) => {
    if (!deadline) return null;
    const date = new Date(deadline);
    const now = new Date();
    const diffTime = date - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    const formatted = date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

    if (diffDays < 0) return { text: formatted, label: `${Math.abs(diffDays)} hari terlewat`, isOverdue: true };
    if (diffDays === 0) return { text: formatted, label: 'Hari ini', isOverdue: false };
    if (diffDays === 1) return { text: formatted, label: 'Besok', isOverdue: false };
    return { text: formatted, label: `${diffDays} hari lagi`, isOverdue: false };
};

const deadline = formatDeadline(props.task.deadline);
</script>

<template>
    <div class="task-card" :class="{ 'is-completed': task.status === 'completed' }">
        <div class="task-card-inner">
            <!-- Checkbox -->
            <button
                @click="$emit('toggle-complete', task.id)"
                class="task-checkbox"
                :class="{ checked: task.status === 'completed' }"
            >
                <svg v-if="task.status === 'completed'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </button>

            <!-- Content -->
            <div class="task-content">
                <div class="task-header">
                    <h3 class="task-title" :class="{ 'line-through': task.status === 'completed' }">
                        {{ task.title }}
                    </h3>
                    <Link :href="route('tasks.edit', task.id)" class="task-edit-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                            <path d="m15 5 4 4"/>
                        </svg>
                    </Link>
                </div>

                <p v-if="task.description" class="task-description">{{ task.description }}</p>

                <div class="task-meta">
                    <!-- Category Badge -->
                    <span
                        v-if="task.category"
                        class="task-badge category-badge"
                        :style="{ backgroundColor: task.category.color + '20', color: task.category.color, borderColor: task.category.color + '40' }"
                    >
                        {{ task.category.name }}
                    </span>

                    <!-- Priority Badge -->
                    <span
                        class="task-badge"
                        :style="{
                            backgroundColor: priorityColors[task.priority].bg,
                            color: priorityColors[task.priority].text,
                            borderColor: priorityColors[task.priority].border
                        }"
                    >
                        {{ priorityLabels[task.priority] }}
                    </span>

                    <!-- Deadline -->
                    <span v-if="deadline" class="task-deadline" :class="{ 'is-overdue': deadline.isOverdue }">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        {{ deadline.label }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.task-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1rem 1.25rem;
    transition: all 0.2s ease;
}

.task-card:hover {
    border-color: #c7d2fe;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.08);
}

.task-card.is-completed {
    opacity: 0.65;
    background: #f8fafc;
}

.task-card-inner {
    display: flex;
    gap: 0.875rem;
    align-items: flex-start;
}

.task-checkbox {
    flex-shrink: 0;
    width: 1.375rem;
    height: 1.375rem;
    border: 2px solid #cbd5e1;
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-top: 0.125rem;
}

.task-checkbox:hover {
    border-color: #6366f1;
}

.task-checkbox.checked {
    background: #6366f1;
    border-color: #6366f1;
    color: white;
}

.task-content {
    flex: 1;
    min-width: 0;
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.5rem;
}

.task-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
    line-height: 1.4;
}

.task-title.line-through {
    text-decoration: line-through;
    color: #94a3b8;
}

.task-edit-btn {
    flex-shrink: 0;
    color: #94a3b8;
    padding: 0.25rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.task-edit-btn:hover {
    color: #6366f1;
    background: #eef2ff;
}

.task-description {
    font-size: 0.8rem;
    color: #64748b;
    margin: 0.25rem 0 0;
    line-height: 1.4;
}

.task-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.625rem;
    align-items: center;
}

.task-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.15rem 0.5rem;
    font-size: 0.7rem;
    font-weight: 600;
    border-radius: 9999px;
    border: 1px solid;
    white-space: nowrap;
}

.task-deadline {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: #64748b;
}

.task-deadline.is-overdue {
    color: #dc2626;
    font-weight: 600;
}
</style>
