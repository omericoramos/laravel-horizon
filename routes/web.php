<?php

use App\Http\Controllers\JobsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/high',[JobsController::class,'index'])->name('high');
Route::get('/low',[JobsController::class,'jobLow'])->name('low');
Route::get('/default',[JobsController::class,'jobDefault'])->name('defautl');
