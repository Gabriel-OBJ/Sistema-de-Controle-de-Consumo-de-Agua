<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
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
        // Usa paginação com estilo Bootstrap 5
        Paginator::useBootstrapFive();

        // Define locale do Carbon para português do Brasil
        Carbon::setLocale('pt_BR');
    }
}
