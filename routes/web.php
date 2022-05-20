<?php

use App\Http\Controllers\ComercialController;
use App\Http\Controllers\HomeController;
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

/*Route::get('/', function () {
    return view('welcome');
});*/

//Public Routes
//Home
Route::get('/', [HomeController::class, 'index'])->name('home');

//Comercial
Route::get('/comercial', [ComercialController::class, 'index'])->name('comercial.index');
Route::post('/comercial/relatorio', [ComercialController::class, 'show_relatorio'])->name('comercial.relatorio');
Route::post('/comercial/grafico', [ComercialController::class, 'show_grafico'])->name('comercial.grafico');
Route::post('/comercial/pizza', [ComercialController::class, 'show_pizza'])->name('comercial.pizza');
