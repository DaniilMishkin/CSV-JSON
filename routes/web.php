<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('auth.login.page'));

Route::controller(AuthController::class)
    ->as('auth.')
    ->group(function () {
        Route::middleware('guest')
            ->group(function () {
                Route::get('/login', 'pageLogin')->name('login.page');
                Route::post('/login', 'handlerLogin')->name('login.handler')
                    ->middleware(['throttle:login']);
            });

        Route::middleware('auth')
            ->group(function () {
                Route::post('/logout', 'logout')->name('logout');
            });
    });

Route::controller(UploadController::class)
    ->as('uploads.')
    ->group(function () {
        Route::get('/', 'list')->name('page.list');
    });

Route::middleware('auth')
    ->group(function () {
        Route::controller(UploadController::class)
            ->prefix('uploads')
            ->as('uploads.')
            ->group(function () {
                Route::get('/create', 'create')->name('page.create');

                Route::as('ajax.')
                    ->group(function () {
                        Route::post('/', 'store')->name('store');
                        Route::get('/{upload}/download', 'download')->name('download');
                    });
            });
    });
