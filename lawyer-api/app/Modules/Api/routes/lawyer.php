<?php

Route::group(['namespace' => 'Api\Http\Controllers\V1\Lawyer', 'prefix' => 'api/v1/lawyer'], function () {

    Route::group(['prefix' => 'data'], function () {
        Route::get('/', 'DataController@index')->name('api.v1.lawyer.data.list');
        Route::get('/countries/cities', 'DataController@cities')->name('api.v1.lawyer.data.cities.list');
        Route::get('/terms', 'DataController@terms')->name('api.v1.lawyer.data.terms.get');
        Route::get('/faq', 'DataController@faq')->name('api.v1.lawyer.data.faq.list');
    });

    Route::group(['middleware' => [\Api\Http\Middleware\ApiAuth::class],], function () {
        Route::group(['middleware' => \Api\Http\Middleware\IsLawyer::class], function () {

            Route::group(['prefix' => 'lawyers'], function () {
                Route::get('/favorites', 'FavoriteLawyersController@index')->name('api.v1.lawyer.favoriteLawyers.list');
                Route::post('/favorites', 'FavoriteLawyersController@create')->name('api.v1.lawyer.favoriteLawyers.add');
                Route::delete('/favorites', 'FavoriteLawyersController@delete')->name('api.v1.lawyer.favoriteLawyers.delete');


                Route::get('/', 'LawyersController@index')->name('api.v1.lawyer.lawyers.list');
                Route::get('/names', 'LawyersController@listNames')->name('api.v1.lawyer.lawyers.list.names');
                Route::get('/me', 'LawyersController@me')->name('api.v1.lawyer.lawyers.me');
                Route::get('/{lawyer_id}', 'LawyersController@get')->name('api.v1.lawyer.lawyers.get');

                Route::get('/{lawyer_id}/reviews', 'ReviewsController@index')->name('api.v1.lawyer.reviews.list');
            });

            Route::group(['prefix' => 'cases'], function () {
                Route::get('/', 'CasesController@index')->name('api.v1.lawyer.cases.list');
                Route::get('/titles', 'CasesController@listTitles')->name('api.v1.lawyer.cases.list.titles');
                Route::get('/{case_id}', 'CasesController@get')->name('api.v1.lawyer.cases.get');

                Route::put('/{case_id}/request', 'CasesController@accept')->name('api.lawyer.v1.cases.accept');
                Route::delete('/{case_id}/request', 'CasesController@dismiss')->name('api.lawyer.v1.cases.dismiss');
            });


            Route::group(['prefix' => 'profile'], function () {
                Route::put('/', 'ProfileController@update')->name('api.v1.lawyer.profile.update');
            });
        });
    });
});