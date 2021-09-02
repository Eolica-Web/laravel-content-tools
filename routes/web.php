<?php

use Illuminate\Support\Facades\Route;
use Eolica\LaravelContentTools\Http\Controllers\TranslationsPostController;

Route::post('/translations', TranslationsPostController::class)
    ->name('translations_post');
