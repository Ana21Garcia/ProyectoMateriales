<?php

namespace App\Providers;

use App\Repositories\PerfilRepository;
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
        // Usuario actual disponible en la plantilla y en la página de ajustes.
        View::composer(['layouts.app', 'ajustes.index'], function ($view) {
            $view->with('perfilActual', app(PerfilRepository::class)->actual());
        });
    }
}
