<?php

/*
|--------------------------------------------------------------------------
| Package Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/
Route::name('webhooks.')->prefix('webhooks')->group(function () {
    Route::post('sns', 'SNSCommunicationsWebhookController')->name('sns');
});