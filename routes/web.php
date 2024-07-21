<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DocumentController::class, 'showForm'])->name('document.form');
Route::post('/upload', [DocumentController::class, 'upload'])->name('document.upload');
Route::post('/search', [DocumentController::class, 'search'])->name('document.search');
