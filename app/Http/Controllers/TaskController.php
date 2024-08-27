<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with('creator')->paginate(15);

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = TaskStatus::all();
        $users = User::all();
        return view('tasks.create', compact('statuses', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:task_statuses,id',
            'assigned_to_id' => 'nullable',
            'created_by_id' => 'required',
        ]);

        Task::create($validatedData);

        flash(__('tasks.task created successfully!'))->success();
        return redirect()->route('tasks.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::with('creator')->findOrFail($id);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $task = Task::findOrFail($id);

        $statuses = TaskStatus::all();
        $users = User::all();
        return view('tasks.edit', compact('task', 'statuses', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'exists:task_statuses,id',
            'assigned_to_id' => 'nullable',
        ]);


        $task->update([
            'name' => $validatedData['name'] ?? $task->name,
            'description' => $validatedData['description'] ?? $task->description,
            'status_id' => $validatedData['status_id'] ?? $task->status_id,
            'assigned_to_id' => $validatedData['assigned_to_id'] ?? $task->assigned_to_id,
        ]);

        flash(__('tasks.task updated successfully!'))->success();
        return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('delete', $task);

        try {
            $task->delete();
            flash(__('tasks.task deleted successfully!'))->success();
            return redirect()->route('tasks.index');
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->route('tasks.index');
        }
    }
}
