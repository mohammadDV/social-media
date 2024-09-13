<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use App\Libraries\OpenAI\Client as OpenAiClient;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('openaiClient', function () {
            return new OpenAiClient();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // DB::listen(function ($query) {
            // You can log queries here or increase a counter
        //     Log::info("Query executed: " . $query->sql);
        // });
        App::setlocale('fa');
    }
}
