<?php

Route::group(['namespace' => 'Api\Http\Controllers\V1\User', 'prefix' => 'api/v1/user'], function () {

    Route::group(['prefix' => 'data'], function () {
        Route::get('/', 'DataController@index')->name('api.v1.data.list');
        Route::get('/countries/cities', 'DataController@cities')->name('api.v1.data.cities.list');
        Route::get('/terms', 'DataController@terms')->name('api.v1.data.terms.get');
        Route::get('/faq', 'DataController@faq')->name('api.v1.data.faq.list');
    });

    Route::group(['middleware' => \Api\Http\Middleware\ApiAuth::class], function () {
        Route::group(['middleware' => \Api\Http\Middleware\IsClient::class], function () {
            Route::group(['prefix' => 'lawyers'], function () {
                Route::get('/favorites', 'FavoriteLawyersController@index')->name('api.v1.favoriteLawyers.list');
                Route::post('/favorites', 'FavoriteLawyersController@create')->name('api.v1.favoriteLawyers.add');
                Route::delete('/favorites', 'FavoriteLawyersController@delete')->name('api.v1.favoriteLawyers.delete');


                Route::get('/', 'LawyersController@index')->name('api.v1.lawyers.list');
                Route::get('/names', 'LawyersController@listNames')->name('api.v1.lawyers.list.names');
                Route::get('/{lawyer_id}', 'LawyersController@get')->name('api.v1.lawyers.get');

                Route::get('/{lawyer_id}/reviews', 'ReviewsController@index')->name('api.v1.reviews.list');
                Route::post('/{lawyer_id}/reviews', 'ReviewsController@create')->name('api.v1.reviews.create');

            });

            Route::group(['prefix' => 'cases'], function () {
                Route::post('/', 'CasesController@create')->name('api.v1.cases.create');
                Route::get('/', 'CasesController@index')->name('api.v1.cases.list');
                Route::get('/titles', 'CasesController@listTitles')->name('api.v1.cases.list.titles');
                Route::get('/{case_id}', 'CasesController@get')->name('api.v1.cases.get');
                Route::delete('/{case_id}', 'CasesController@delete')->name('api.v1.cases.delete');
                Route::post('/{case_id}/lawyers', 'CasesController@assignLawyer')->name('api.v1.cases.assignLawyer');
                Route::delete('/{case_id}/lawyers', 'CasesController@dismissLawyer')->name('api.v1.cases.dismissLawyer');
                Route::patch('/{case_id}/close', 'CasesController@close')->name('api.v1.cases.close');
                Route::patch('/{case_id}/reopen', 'CasesController@reopen')->name('api.v1.cases.reopen');
            });

            Route::group(['prefix' => 'attachments'], function () {
                Route::delete('/{attachment_id}', 'AttachmentsController@delete')->name('api.v1.attachments.delete');
            });
        });
    });
});