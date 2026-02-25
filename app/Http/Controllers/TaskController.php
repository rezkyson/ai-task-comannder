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
    public function index(Request $request)
    {
        $query = $request->user()->tasks()->with('category');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
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

    public function create()
    {
        return Inertia::render('Tasks/Create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        $request->user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dibuat!');
    }

    public function edit(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Tasks/Edit', [
            'task' => $task->load('category'),
            'categories' => Category::all(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validated();

        // Handle completed_at timestamp
        if ($data['status'] === 'completed' && $task->status !== 'completed') {
            $data['completed_at'] = now();
        }
        if ($data['status'] !== 'completed') {
            $data['completed_at'] = null;
        }

        $task->update($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil diupdate!');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dihapus!');
    }

    public function toggleComplete(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $isCompleted = $task->status === 'completed';

        $task->update([
            'status' => $isCompleted ? 'pending' : 'completed',
            'completed_at' => $isCompleted ? null : now(),
        ]);

        return back()->with('success', 'Status task diubah!');
    }

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
