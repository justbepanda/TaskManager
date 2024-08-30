<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = QueryBuilder::for(Task::class)
            ->allowedFilters(['status_id', 'created_by_id', 'assigned_to_id'])
            ->with('creator')
            ->paginate(15);


        $taskStatuses = TaskStatus::all();
        $users = User::all();

//        $tasks = Task::with('creator')->paginate(15);

        return view('tasks.index', compact('tasks', 'taskStatuses', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = TaskStatus::all();
        $users = User::all();
        $labels = Label::all();
        return view('tasks.create', compact('statuses', 'users', 'labels'));
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
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id'
        ]);

        $task = new Task();
        $task->fill($validatedData);
        $task->save();
        $task->labels()->attach($validatedData['labels'] ?? []);

        flash(__('tasks.Task created successfully!'))->success();
        return redirect()->route('tasks.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::with('creator', 'labels')->findOrFail($id);

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
        $labels = Label::all();

        return view('tasks.edit', compact('task', 'statuses', 'users', 'labels'));
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
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
        ]);


        $task->update([
            'name' => $validatedData['name'] ?? $task->name,
            'description' => $validatedData['description'] ?? $task->description,
            'status_id' => $validatedData['status_id'] ?? $task->status_id,
            'assigned_to_id' => $validatedData['assigned_to_id'] ?? $task->assigned_to_id,
        ]);

        $task->labels()->sync($validatedData['labels'] ?? []);

        flash(__('tasks.Task updated successfully!'))->success();
        return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('delete', $task);

        $task->delete();
        flash(__('tasks.Task deleted successfully!'))->success();
        return redirect()->route('tasks.index');
    }
}
