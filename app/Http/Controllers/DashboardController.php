<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Stats
        $totalTasks = $user->tasks()->count();
        $completedTasks = $user->tasks()->completed()->count();
        $pendingTasks = $user->tasks()->pending()->count();
        $overdueTasks = $user->tasks()->overdue()->count();

        // Upcoming (5 deadline terdekat)
        $upcomingTasks = $user->tasks()
            ->with('category')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('deadline')
            ->where('deadline', '>=', now())
            ->orderBy('deadline', 'asc')
            ->limit(5)
            ->get();

        // Task per kategori
        $tasksByCategory = Category::withCount([
            'tasks' => fn($q) => $q->where('user_id', $user->id)
        ])
            ->having('tasks_count', '>', 0)
            ->get()
            ->map(fn($cat) => [
                'name' => $cat->name,
                'count' => $cat->tasks_count,
                'color' => $cat->color,
            ]);

        // 5 task terbaru
        $recentTasks = $user->tasks()
            ->with('category')
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => $totalTasks,
                'completed' => $completedTasks,
                'pending' => $pendingTasks,
                'overdue' => $overdueTasks,
            ],
            'upcomingTasks' => $upcomingTasks,
            'tasksByCategory' => $tasksByCategory,
            'recentTasks' => $recentTasks,
        ]);
    }
}
