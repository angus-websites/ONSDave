<?php

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\TimeRecordController;
use App\Http\Controllers\TodayController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StorageController;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('index');

//Storage
Route::get('/images/{path}', [StorageController::class, 'image'])->where('path', '.*');
Route::get('/storage/{path}', [StorageController::class, 'storage'])->where('path', '.*');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Today
    Route::get('/today', [TodayController::class, 'index'])->name('today');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::post('/history/day/fetch', [HistoryController::class, 'fetchByDate'])->name('history.day.fetch');
    Route::post('/history/month/fetch', [HistoryController::class, 'fetchByMonth'])->name('history.month.fetch');


    Route::resource('time-records', TimeRecordController::class)->only(['store', 'update', 'destroy']);

});
