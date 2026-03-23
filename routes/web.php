<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ResearchPaperController; // ✅ Add this

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/dashboard', [GroupController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::post('/groups/{group}/invite', [GroupController::class, 'invite'])->name('groups.invite');

    // ✅ Make sure these are here too
    Route::get('/groups/{group}', [ResearchPaperController::class, 'show'])->name('groups.show');
    Route::post('/groups/{group}/papers', [ResearchPaperController::class, 'store'])->name('papers.store');
    Route::get('/papers/{paper}/download', [ResearchPaperController::class, 'download'])->name('papers.download');
    Route::delete('/papers/{paper}', [ResearchPaperController::class, 'destroy'])->name('papers.destroy');
});

require __DIR__.'/auth.php';