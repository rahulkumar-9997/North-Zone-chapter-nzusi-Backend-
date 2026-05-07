<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\BlogController;

Route::get('blog-category', [BlogController::class, 'blogCategory']);
Route::get('blog-category/{slug}', [BlogController::class, 'categoryWiseBlogList']);
Route::get('blogs', [BlogController::class, 'blogList']);
