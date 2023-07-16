<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/test', function () {
    return "This is Test API for Scholer CRM";
});
Route::post('/add_new_product', [ProductController::class, 'add_new_product']);
Route::post('/get_products', [ProductController::class, 'get_products']);
Route::post('/get_category', [ProductController::class, 'get_category']);
Route::post('/get_subjects', [ProductController::class, 'get_subjects']);
Route::post('/get_sheet_sizes', [ProductController::class, 'get_sheet_sizes']);
Route::post('/get_book_for_board', [ProductController::class, 'get_book_for_board']);

// Routes for Category Form
Route::post('/add_new_category', [ProductController::class, 'add_new_category']);

// Routes for Subject Form
Route::post('/add_new_subject', [ProductController::class, 'add_new_subject']);

