<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('brands' , BrandController::class);

Route::apiResource('categories' , CategoryController::class);
