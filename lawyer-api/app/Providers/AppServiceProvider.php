<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Rollbar\Rollbar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if (!app()->isLocal()) {
            URL::forceScheme('https');
        }


        if (app()->isLocal()) {
            DB::enableQueryLog();
        }

        if (in_array(app()->environment(), ['staging', 'production'])) {
            $this->setupRollbar();
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    private function setupRollbar()
    {
        Rollbar::init([
            'access_token' => env('ROLLBAR_API_KEY'),
            'environment' => app()->environment(),
            'code_version' => file_exists(base_path('REVISION')) ? file_get_contents(base_path('REVISION')) : 'dev',
        ]);
    }
}
