<?php

/*
|--------------------------------------------------------------------------
| Package Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register api routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "api" middleware group.
|
*/
Route::middleware(config('snscommunicationrecords.api_middleware'))->get('communication-records', 'SNSCommunicationRecordsController');