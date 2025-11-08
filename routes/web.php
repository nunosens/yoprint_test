<?php

use App\Http\Controllers\CsvUploadController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CsvUploadController::class, 'index']);
Route::post('/upload', [CsvUploadController::class, 'store'])->name('upload.csv');
Route::get('/uploads', [CsvUploadController::class, 'getUploads'])->name('uploads.list');

// Routes untuk products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/data', [ProductController::class, 'getProducts'])->name('products.data');