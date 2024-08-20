<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/task_statuses/create', [TaskStatusController::class, 'create'])->name('task_statuses.create');
    Route::post('/task_statuses', [TaskStatusController::class, 'store'])->name('task_statuses.store');

    Route::get('/task_statuses/{id}/edit', [TaskStatusController::class, 'edit'])->name('task_statuses.edit');
    Route::put('/task_statuses/{id}', [TaskStatusController::class, 'update'])->name('task_statuses.update');
    Route::delete('/task_statuses/{id}', [TaskStatusController::class, 'destroy'])->name('task_statuses.destroy');
});

Route::get('/task_statuses', [TaskStatusController::class, 'index'])->name('task_statuses.index');
Route::get('/task_statuses/{id}', [TaskStatusController::class, 'show'])->name('task_statuses.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/task/create', [TaskController::class, 'create'])->name('task.create');
    Route::post('/task', [TaskController::class, 'store'])->name('task.store');

    Route::get('/task/{id}/edit', [TaskController::class, 'edit'])->name('task.edit');
    Route::put('/task/{id}', [TaskController::class, 'update'])->name('task.update');
    Route::delete('/task/{id}', [TaskController::class, 'destroy'])->name('task.destroy');
});

Route::get('/task', [TaskController::class, 'index'])->name('task.index');
Route::get('/task/{id}', [TaskController::class, 'show'])->name('task.show');

require __DIR__.'/auth.php';
