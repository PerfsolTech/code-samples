<?php

namespace Acme\WeeklyVisitors;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class CardServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            app()->setLocale(Auth::user()->lang);
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('weekly-visitors', __DIR__ . '/../dist/js/card.js');
            Nova::style('weekly-visitors', __DIR__ . '/../dist/css/card.css');
            Nova::translations(static::getTranslations());
        });
    }


    /**
     * Get the translation keys from file.
     *
     * @return array
     */
    private static function getTranslations()
    {
        $translationFile = __DIR__ . '../resources/lang/' . app()->getLocale() . '.json';

        if (!is_readable($translationFile)) {
            return [];
        }

        return json_decode(file_get_contents($translationFile), true);
    }


    /**
     * Register the card's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/weekly-visitors')
            ->group(__DIR__ . '/../routes/api.php');
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
}
