<?php

namespace App\Providers;

//use App\Services\ExportService as ExportService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
	    $this->app->bind('App\Services\ExportService', function ($app) {
		    return new ExportService();
	    });
    }
}
