<?php

namespace App\Http\Controllers;

use App\Models\TaskStatus;
use Illuminate\Http\Request;

class TaskStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $task_statuses = TaskStatus::all();
        return view('task_statuses.index', compact('task_statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('task_statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:task_statuses,name|max:255',
        ]);

        TaskStatus::create($validatedData);

        flash(__('task_statuses.Status created successfully!'))->success();
        return redirect()->route('task_statuses.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $taskStatus = TaskStatus::findOrFail($id);
        return view('task_statuses.show', compact('taskStatus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $taskStatus = TaskStatus::findOrFail($id);
        return view('task_statuses.edit', compact('taskStatus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:task_statuses,name,' . $id . '|max:255',
        ]);

        $taskStatus = TaskStatus::findOrFail($id);

        $taskStatus->name = $validatedData['name'];
        $taskStatus->save();

        flash(__('task_statuses.Status updated successfully!'))->success();
        return redirect()->route('task_statuses.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $taskStatus = TaskStatus::findOrFail($id);

        if ($taskStatus->tasks()->exists()) {
            flash(__('task_statuses.The status cannot be deleted because it is associated with tasks.'))->error();
            return redirect()->route('task_statuses.index');
        }

        $taskStatus->delete();
        flash(__('task_statuses.Status deleted successfully!'))->success();
        return redirect()->route('task_statuses.index');
    }
}
