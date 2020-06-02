<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/redirect', 'CalenderLogin@redirect');

Route::get('/callback', 'CalenderLogin@handleProviderCallback');

Route::get('/events', 'CalenderLogin@GetCalendarEvents');

Route::get('/create', 'CalenderLogin@CreateEventCalendar');
