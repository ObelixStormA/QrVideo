<?php

namespace App\Providers;

use App\Models\User;
use App\View\Composers\SidebarComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Bypass keeps Super Admin working even for permissions created after
        // the last seed run, before they've been synced to the role.
        Gate::before(fn (User $user, string $ability): ?bool => $user->hasRole('Super Admin') ? true : null);

        View::composer(['partials.sidebar', 'videos.index'], SidebarComposer::class);

        Paginator::useBootstrapFive();
    }
}
