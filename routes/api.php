<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('brands' , BrandController::class);
Route::get('/brands/{brand}/products', [BrandController::class, 'products']);

Route::apiResource('categories' , CategoryController::class);

Route::get('/categories/{category}/children', [CategoryController::class, 'children']);
Route::get('/categories/{category}/parent', [CategoryController::class, 'parent']);
Route::get('/categories/{category}/products', [CategoryController::class, 'products']);

Route::apiResource('products' , ProductController::class);

