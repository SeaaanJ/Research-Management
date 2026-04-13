<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ResearchPaperController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\PaperCommentController;


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

    // Group routes
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::post('/groups/{group}/invite', [GroupController::class, 'invite'])->name('groups.invite');
    Route::get('/groups/{group}/confirm-delete', [GroupController::class, 'confirmDelete'])->name('groups.confirm-delete');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

    // Email Verification routes
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    //  Invite response routes
    Route::post('/invites/{invite}/accept', [GroupController::class, 'acceptInvite'])->name('invites.accept');
    Route::post('/invites/{invite}/decline', [GroupController::class, 'declineInvite'])->name('invites.decline');

    // Paper routes
    Route::get('/groups/{group}', [ResearchPaperController::class, 'show'])->name('groups.show');
    Route::post('/groups/{group}/papers', [ResearchPaperController::class, 'store'])->name('papers.store');
    Route::get('/papers/{paper}/view', [ResearchPaperController::class, 'view'])->name('papers.view');
    Route::get('/papers/{paper}/download', [ResearchPaperController::class, 'download'])->name('papers.download');
    Route::delete('/papers/{paper}', [ResearchPaperController::class, 'destroy'])->name('papers.destroy');
    Route::post('/papers/{paper}/publish', [ResearchPaperController::class, 'publish'])->name('papers.publish');
    Route::post('/groups/{group}/publish-all', [ResearchPaperController::class, 'publishAll'])->name('papers.publishAll');     // Publish all, Visible to Group Owner, Adviser and Admins only
    Route::post('/groups/{group}/unpublish-all', [ResearchPaperController::class, 'unpublishAll'])->name('papers.unpublishAll');  // UnPublish all, Visible to Group Owner, Adviser and Admins only



    // Comment routes
    Route::get('/papers/{researchPaper}/comments', [PaperCommentController::class, 'index'])->name('comments.index');
    Route::post('/papers/{researchPaper}/comments', [PaperCommentController::class, 'store'])->name('comments.store');
    Route::delete('/papers/{researchPaper}/comments/{comment}', [PaperCommentController::class, 'destroy'])->name('comments.destroy');
        
    // Nav Routes
   Route::get('/groups', function () {$groups = auth()->user()->groups ?? collect(); return view('groupstab', compact('groups')); })->name('groups');

});

require __DIR__.'/auth.php';