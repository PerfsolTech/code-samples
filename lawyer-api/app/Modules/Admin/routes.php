<?php

use Illuminate\Routing\Router;

Admin::registerHelpersRoutes();

Route::group([
    'prefix' => config('admin.prefix'),
    'namespace' => Admin::controllerNamespace(),
    'middleware' => ['web', 'admin'],
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    Route::group([
        'middleware' => 'admin.permission:check,manage-languages',
    ], function ($router) {
        $router->resource('languages', 'LanguagesController');
    });

    Route::group([
        'middleware' => 'admin.permission:check,manage-countries',
    ], function ($router) {
        $router->resource('countries', 'CountriesController');
        $router->get('countries/{country_id}/change-status', 'CountriesController@changeStatus')
            ->name('admin.countries.change-status');
        $router->get('countries/{country_id}/disable-cities', 'CountriesController@disableCities')
            ->name('admin.countries.disableCities');
        $router->get('countries/{country_id}/enabled-cities', 'CountriesController@enableCities')
            ->name('admin.countries.enableCities');
    });


    $router->get('cities/ajax-select-list', 'CitiesController@ajaxSelectList')->name('cities.ajax-select-list');
    Route::group([
        'middleware' => 'admin.permission:check,manage-cities',
    ], function ($router) {
        $router->resource('cities', 'CitiesController');

        $router->get('cities/{city_id}/change-status', 'CitiesController@changeStatus')
            ->name('admin.cities.change-status');
    });

    Route::group([
        'middleware' => 'admin.permission:check,manage-cases',
    ], function ($router) {
        $router->resource('cases', 'CasesController');
        $router->get('cases/{case_id}/reopen', 'CasesController@reopen')
            ->name('admin.cases.reopen');
    });


    Route::group([
        'middleware' => 'admin.permission:check,manage-reviews',
    ], function ($router) {

        $router->resource('reviews', 'ReviewsController');
        $router->get('reviews/{review_id}/approve', 'ReviewsController@approve')
            ->name('admin.reviews.approve');
        $router->get('reviews/{review_id}/decline', 'ReviewsController@decline')
            ->name('admin.reviews.decline');
    });


    Route::group([
        'middleware' => 'admin.permission:check,manage-competency',
    ], function ($router) {
        $router->resource('competencies', 'CompetenciesController');

        $router->get('competencies/{competency_id}/change-status', 'CompetenciesController@changeStatus')
            ->name('admin.competencies.change-status');
    });

    Route::group([
        'middleware' => 'admin.permission:check,manage-users',
    ], function ($router) {
        $router->resource('users', 'UsersController');
        $router->resource('lawyers', 'LawyersController');

        $router->get('users/{user_id}/change-status', 'UsersController@changeStatus')
            ->name('admin.users.change-status');
    });

});
