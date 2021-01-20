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

Route::get('/', \App\Http\Controllers\HomeController::class)
    ->name('home');
Route::post('/', [\App\Http\Controllers\HomeController::class, 'command'])
    ->name('command');

Route::get('/setup', \App\Http\Controllers\SetupController::class);
Route::post('/whatsapp', \App\Http\Controllers\WhatAppController::class)
    ->name('whatsapp')
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
