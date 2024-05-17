<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RedirectIfAuthenticated;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes fori your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::group(['middleware' => ['guest']], function () {
    Route::get('/', function () {
        return view('layout.guest.signup');
    });
    Route::prefix('user')->group(function () {
        Route::post('/signup', [UserController::class, 'register'])->name('signup');
        Route::match(['get', 'post'], '/login', [UserController::class, 'login'])->name('login');
        Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    });
});
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [UserController::class, 'dashboard'])->name('home');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});
