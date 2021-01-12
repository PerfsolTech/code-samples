<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::namespace('App\Http\Controllers')->group(function () {
    Route::post('/create-new-code', 'QRCodesController@createNewCode');
    Route::post('/save-code-changes', 'QRCodesController@saveCodeChanges');
    Route::post('/delete-code', 'QRCodesController@deleteCode');
    Route::get('/codes', 'QRCodesController@getCodes');
});