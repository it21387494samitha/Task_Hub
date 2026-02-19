<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Dashboard;
use App\Livewire\Tasks;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard — now powered by Livewire
Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Task routes — all require auth
Route::middleware('auth')->group(function () {
    Route::get('/tasks', Tasks\Index::class)->name('tasks.index');
    Route::get('/tasks/create', Tasks\Create::class)->name('tasks.create');
    Route::get('/tasks/{task}/edit', Tasks\Edit::class)->name('tasks.edit');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
