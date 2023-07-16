<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ForBoardController;
use App\Http\Controllers\SheetSizeController;
use App\Http\Controllers\PaperController;
use App\Http\Controllers\PaperTypeController;
use App\Http\Controllers\PlateController;
use App\Http\Controllers\GodownController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VoucherController;

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

// Routes for Product Form
Route::post('/add_new_product', [ProductController::class, 'add_new_product']);
Route::post('/get_products', [ProductController::class, 'get_products']);
Route::post('/get_p_f_plates', [ProductController::class, 'get_p_f_plates']);

// Routes for Sheet Size
Route::post('/add_new_sheet_size', [SheetSizeController::class, 'add_new_sheet_size']);
Route::post('/get_sheet_sizes', [SheetSizeController::class, 'get_sheet_sizes']);

// Routes for Book For Board
Route::post('/get_book_for_board', [ForBoardController::class, 'get_book_for_board']);
Route::post('/add_new_book_for_board', [ForBoardController::class, 'add_new_book_for_board']);

// Routes for Category Form
Route::post('/add_new_category', [CategoryController::class, 'add_new_category']);
Route::post('/get_category', [CategoryController::class, 'get_category']);

// Routes for Subject Form
Route::post('/add_new_subject', [SubjectController::class, 'add_new_subject']);
Route::post('/get_subjects', [SubjectController::class, 'get_subjects']);

// Routes for Paper
Route::post('/add_new_paper', [PaperController::class, 'add_new_paper']);
Route::post('/get_papers', [PaperController::class, 'get_papers']);

// Routes for Paper Type
Route::post('/add_new_paper_type', [PaperTypeController::class, 'add_new_paper_type']);
Route::post('/get_paper_types', [PaperTypeController::class, 'get_paper_types']);

// Routes for Plates
Route::post('/add_new_plate', [PlateController::class, 'add_new_plate']);
Route::post('/get_plates', [PlateController::class, 'get_plates']);

// Routes for Godown
Route::post('/add_new_godown', [GodownController::class, 'add_new_godown']);
Route::post('/get_godowns', [GodownController::class, 'get_godowns']);

// Routes for Vendor
Route::post('/add_new_vendor', [VendorController::class, 'add_new_vendor']);
Route::post('/get_vendors', [VendorController::class, 'get_vendors']);
Route::post('/get_vendor_types', [VendorController::class, 'get_vendor_types']);
Route::post('/get_p_p_vendors', [VendorController::class, 'get_p_p_vendors']);

// Routes for Voucher
Route::post('/add_new_voucher', [VoucherController::class, 'add_new_voucher']);


