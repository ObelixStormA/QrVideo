<?php

use App\Http\Controllers\ArViewController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [VideoController::class, 'index'])->name('home');
Route::get('/watch/{video:slug}', [VideoController::class, 'show'])->name('videos.show');
Route::get('/ar/{uuid}', ArViewController::class)->name('ar.show');
