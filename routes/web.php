<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ResearchPaperController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\PaperCommentController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\SuperAdminDashboardController;
use App\Http\Controllers\Admin\AdminBanController;
use App\Http\Controllers\Admin\AdminGroupController;
use App\Http\Controllers\Admin\SuperAdminController;

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('home');


// Email Verification Routes (auth only, NOT verified)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

// Super Admin Routes
Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('super-admin')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('super-admin.dashboard');
    Route::get('/admins/create', [SuperAdminDashboardController::class, 'createAdmin'])->name('super-admin.admins.create');
    Route::post('/admins', [SuperAdminDashboardController::class, 'storeAdmin'])->name('super-admin.admins.store');
});


Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('super-admin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('super-admin.dashboard');
    Route::get('/admins/create', [SuperAdminController::class, 'createAdminForm'])->name('super-admin.admins.create');
    Route::post('/admins', [SuperAdminController::class, 'storeAdmin'])->name('super-admin.admins.store');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/users/{user}/ban', [AdminBanController::class, 'store'])->name('admin.users.ban');
    Route::delete('/users/{user}/ban', [AdminBanController::class, 'destroy'])->name('admin.users.unban');
    Route::get('/groups', [AdminGroupController::class, 'index'])->name('admin.groups.index');
    Route::get('/groups/{group}', [AdminGroupController::class, 'show'])->name('admin.groups.show');
});

// Regular User Routes
Route::middleware(['auth', 'verified', 'banned'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Groups
    Route::get('/groups', function () {
        $groups = auth()->user()->groups ?? collect();
        return view('groupstab', compact('groups'));
    })->name('groups');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::post('/groups/{group}/invite', [GroupController::class, 'invite'])->name('groups.invite');
    Route::get('/groups/{group}/confirm-delete', [GroupController::class, 'confirmDelete'])->name('groups.confirm-delete');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

    // Invites
    Route::post('/invites/{invite}/accept', [GroupController::class, 'acceptInvite'])->name('invites.accept');
    Route::post('/invites/{invite}/decline', [GroupController::class, 'declineInvite'])->name('invites.decline');

    // Papers
    Route::get('/groups/{group}', [ResearchPaperController::class, 'show'])->name('groups.show');
    Route::post('/groups/{group}/papers', [ResearchPaperController::class, 'store'])->name('papers.store');
    Route::get('/papers/{paper}/view', [ResearchPaperController::class, 'view'])->name('papers.view');
    Route::get('/papers/{paper}/download', [ResearchPaperController::class, 'download'])->name('papers.download');
    Route::delete('/papers/{paper}', [ResearchPaperController::class, 'destroy'])->name('papers.destroy');
    Route::post('/papers/{paper}/publish', [ResearchPaperController::class, 'publish'])->name('papers.publish');
    Route::post('/groups/{group}/publish-all', [ResearchPaperController::class, 'publishAll'])->name('papers.publishAll');
    Route::post('/groups/{group}/unpublish-all', [ResearchPaperController::class, 'unpublishAll'])->name('papers.unpublishAll');
    Route::post('/papers/{researchPaper}/publish-with-abstract', [ResearchPaperController::class, 'publishWithAbstract'])->name('papers.publishWithAbstract');
    Route::post('/papers/{researchPaper}/unpublish', [ResearchPaperController::class, 'unpublish'])->name('papers.unpublish');
    // Comments
    Route::get('/papers/{researchPaper}/comments', [PaperCommentController::class, 'index'])->name('comments.index');
    Route::post('/papers/{researchPaper}/comments', [PaperCommentController::class, 'store'])->name('comments.store');
    Route::delete('/papers/{researchPaper}/comments/{comment}', [PaperCommentController::class, 'destroy'])->name('comments.destroy');

    // Explore
    Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
    Route::get('/explore/search', [ExploreController::class, 'search'])->name('explore.search');
});

require __DIR__.'/auth.php';