<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\GroupInvite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
  public function boot(): void
    {

        View::composer('layouts.navigation', function ($view) {
            if (Auth::check()) {
                $pendingInvites = GroupInvite::where('receiver_id', Auth::user()->id)
                    ->where('status', 'pending')
                    ->get();
                
                $view->with('pendingInvites', $pendingInvites);
            }
        });
    }
}