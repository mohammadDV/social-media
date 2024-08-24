<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use App\Libraries\OpenAI\Client as OpenAiClient;

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
        App::setlocale('fa');
    }
}
