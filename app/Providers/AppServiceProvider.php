<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;

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

        RedirectIfAuthenticated::redirectUsing(function(){
            return route('scrum-board');
        });

        Authenticate::redirectUsing(function(){
            Session::flash('fail', 'Log in first');
            return route('login');
        });
    }
}
