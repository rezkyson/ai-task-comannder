<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\AiParseService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of all tasks for the authenticated user.
     */
    public function index(Request $request)
    {
        $query = $request->user()->tasks()->with('category');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'categories' => $categories,
            'filters' => $request->only(['status', 'category_id', 'priority', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $categories = Category::all();

        return Inertia::render('Tasks/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request)
    {
        $request->user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dibuat!');
    }

    /**
     * Show the form for editing a task.
     */
    public function edit(Task $task)
    {
        // Ensure user can only edit their own tasks
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $categories = Category::all();

        return Inertia::render('Tasks/Edit', [
            'task' => $task->load('category'),
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validated();

        // If status changed to completed, set completed_at
        if ($data['status'] === 'completed' && $task->status !== 'completed') {
            $data['completed_at'] = now();
        }

        // If status changed from completed, clear completed_at
        if ($data['status'] !== 'completed') {
            $data['completed_at'] = null;
        }

        $task->update($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil diupdate!');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dihapus!');
    }

    /**
     * Toggle task completion status.
     */
    public function toggleComplete(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        if ($task->status === 'completed') {
            $task->update([
                'status' => 'pending',
                'completed_at' => null,
            ]);
        } else {
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        return back()->with('success', 'Status task diubah!');
    }

    /**
     * Parse natural language input using AI.
     */
    public function parse(Request $request, AiParseService $aiService)
    {
        $request->validate([
            'input' => ['required', 'string', 'max:500'],
        ]);

        $parsed = $aiService->parseTaskInput($request->input('input'));

        return response()->json([
            'success' => true,
            'data' => $parsed,
        ]);
    }
}
