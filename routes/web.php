<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\GcalendarController;
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

Route::get('/', [EventController::class, 'index'])
    ->middleware('auth');

Route::resource('events', EventController::class)
    ->only('index', 'store', 'update', 'destroy', 'show')
    ->middleware('auth');

Route::resource('Gcalendar', GcalendarController::class)
    ->except('create', 'edit');

Route::get('oauth', [GcalendarController::class, 'oauth']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
