<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\LabelController;
use Illuminate\Support\Facades\Route;

/*Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // task_statuses
    Route::get('/task_statuses/create', [TaskStatusController::class, 'create'])->name('task_statuses.create');
    Route::post('/task_statuses', [TaskStatusController::class, 'store'])->name('task_statuses.store');

    Route::get('/task_statuses/{id}/edit', [TaskStatusController::class, 'edit'])->name('task_statuses.edit');
    Route::put('/task_statuses/{id}', [TaskStatusController::class, 'update'])->name('task_statuses.update');
    Route::delete('/task_statuses/{id}', [TaskStatusController::class, 'destroy'])->name('task_statuses.destroy');

    // tasks
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

    Route::get('/tasks/{id}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::patch('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // labels
    Route::get('/labels/create', [LabelController::class, 'create'])->name('labels.create');
    Route::post('/labels', [LabelController::class, 'store'])->name('labels.store');

    Route::get('/labels/{label}/edit', [LabelController::class, 'edit'])->name('labels.edit');
    Route::patch('/labels/{label}', [LabelController::class, 'update'])->name('labels.update');
    Route::delete('/labels/{label}', [LabelController::class, 'destroy'])->name('labels.destroy');
});

// task_statuses
Route::get('/task_statuses', [TaskStatusController::class, 'index'])->name('task_statuses.index');
Route::get('/task_statuses/{id}', [TaskStatusController::class, 'show'])->name('task_statuses.show');

// tasks
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');

// labels
Route::get('/labels', [LabelController::class, 'index'])->name('labels.index');
Route::get('/labels/{label}', [LabelController::class, 'show'])->name('labels.show');

require __DIR__ . '/auth.php';
