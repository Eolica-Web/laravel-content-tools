<?php

use Illuminate\Support\Facades\Route;
use Eolica\LaravelContentTools\Http\Controllers;
use Eolica\LaravelContentTools\Http\Middleware;

Route::post('/translations', Controllers\TranslationsPostController::class)
    ->middleware(Middleware\CheckPermission::class)
    ->name('translations_post');
