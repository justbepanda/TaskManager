<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @property int $id
 * @property string $name
 * @property int|null $status_id
 * @property int|null $created_by_id
 * @property int|null $assigned_to_id
 * @property string|null $description
 */
class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = QueryBuilder::for(Task::class)
            ->allowedFilters(
                [
                    AllowedFilter::exact('status_id'),
                    AllowedFilter::exact('created_by_id'),
                    AllowedFilter::exact('assigned_to_id')
                ]
            )
            ->with('creator')
            ->paginate();


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
            'name' => 'required|unique:tasks,name|max:255',
            'status_id' => 'required',
            'assigned_to_id' => 'nullable',
            'description' => 'max:255',
            'labels' => ''
        ]);

        $validatedData['created_by_id'] = Auth::id();

        $task = new Task();
        $task->fill($validatedData);
        $task->save();

        if (isset($validatedData['labels'])) {
            $task->labels()->attach($validatedData['labels']);
        }

        flash(__('tasks.Task created successfully!'))->success();
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
            'labels' => '',
        ]);


        $task->fill($validatedData);
        $task->save();

        if (isset($validatedData['labels'])) {
            $task->labels()->sync($validatedData['labels']);
        }

        flash(__('tasks.Task updated successfully!'))->success();
        return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('delete', $task);

        $task->delete();
        flash(__('tasks.Task deleted successfully!'))->success();
        return redirect()->route('tasks.index');
    }
}
