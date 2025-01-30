<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        Inertia::share([
            'auth' => function () {
                return [
                    'user' => Auth::check() ? [
                        'id' => Auth::id(),
                        'name' => Auth::user()->name,
                        'email' => (Auth::user()->email) ? Auth::user()->email :
                        ((Auth::user()->google_email) ? Auth::user()->google_email :
                        Auth::user()->slack_email),
                        'avatar' => Auth::user()->avatar,
                    ] : null,
                ];
            },
        ]);
    }

    
}
