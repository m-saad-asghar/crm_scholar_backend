<?php

use App\Http\Controllers\AuthController;
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
use App\Http\Controllers\BatchController;
use App\Http\Controllers\POPressController;
use App\Http\Controllers\POLaminationController;
use App\Http\Controllers\AccountVoucherController;
use App\Http\Controllers\POBinderController;
use App\Http\Controllers\BookReceiptController;
use App\Http\Controllers\UserController;

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
    return "This is Test API for Scholar CRM";
});


Route::group(['prefix' => 'auth'], function($router){
    Route::post('/register', [AuthController::class, 'register']);
    Route::any('/login', [AuthController::class, 'login'])->name("login");
});
//Route::group(['middleware' => ['auth:api', 'jwt.refresh']], function () {

Route::group(['middleware' => 'auth:api'], function($router){
    Route::get('/auth/logout', [AuthController::class, 'logout']);

    // Routes for Product Form
Route::post('/add_new_product', [ProductController::class, 'add_new_product']);
// Route::post('/get_products', [ProductController::class, 'get_products']);
Route::post('/get_p_f_plates', [ProductController::class, 'get_p_f_plates']);
Route::put('/update_product/{id}', [ProductController::class, 'update_product']);
Route::put('/change_status_product/{id}', [ProductController::class, 'change_status_product']);
Route::post('/get_products_for_batch', [ProductController::class, 'get_products_for_batch']);

// Routes for Sheet Size
Route::post('/add_new_sheet_size', [SheetSizeController::class, 'add_new_sheet_size']);
Route::post('/get_sheet_sizes', [SheetSizeController::class, 'get_sheet_sizes']);
Route::put('/update_sheet_size/{id}', [SheetSizeController::class, 'update_sheet_size']);
Route::put('/change_status_sheet_size/{id}', [SheetSizeController::class, 'change_status_sheet_size']);


// Routes for Book For Board
Route::post('/get_book_for_board', [ForBoardController::class, 'get_book_for_board']);
Route::post('/add_new_book_for_board', [ForBoardController::class, 'add_new_book_for_board']);
Route::put('/update_book_for_board/{id}', [ForBoardController::class, 'update_book_for_board']);
Route::put('/change_status_book_for_board/{id}', [ForBoardController::class, 'change_status_book_for_board']);


// Routes for Category Form
Route::post('/add_new_category', [CategoryController::class, 'add_new_category']);
Route::post('/get_category', [CategoryController::class, 'get_category']);
Route::put('/update_category/{id}', [CategoryController::class, 'update_category']);
Route::put('/change_status_category/{id}', [CategoryController::class, 'change_status_category']);


// Routes for Subject Form
Route::post('/add_new_subject', [SubjectController::class, 'add_new_subject']);
Route::post('/get_subjects', [SubjectController::class, 'get_subjects']);
Route::put('/update_subject/{id}', [SubjectController::class, 'update_subject']);
Route::put('/change_status_subject/{id}', [SubjectController::class, 'change_status_subject']);


// Routes for Paper
Route::post('/add_new_paper', [PaperController::class, 'add_new_paper']);
Route::post('/get_papers', [PaperController::class, 'get_papers']);
Route::post('/get_paper_with_id/{id}', [PaperController::class, 'get_paper_with_id']);
Route::post('/get_paper_with_type', [PaperController::class, 'get_paper_with_type']);
Route::put('/update_paper/{id}', [PaperController::class, 'update_paper']);
Route::put('/change_status_paper/{id}', [PaperController::class, 'change_status_paper']);


// Routes for Paper Type
Route::post('/add_new_paper_type', [PaperTypeController::class, 'add_new_paper_type']);
Route::post('/get_paper_types', [PaperTypeController::class, 'get_paper_types']);
Route::put('/update_paper_type/{id}', [PaperTypeController::class, 'update_paper_type']);
Route::put('/change_status_paper_type/{id}', [PaperTypeController::class, 'change_status_paper_type']);



// Routes for Plates
Route::post('/add_new_plate', [PlateController::class, 'add_new_plate']);
Route::post('/get_plates', [PlateController::class, 'get_plates']);
Route::post('/get_plate_with_id', [PlateController::class, 'get_plate_with_id']);
Route::put('/update_plate/{id}', [PlateController::class, 'update_plate']);
Route::put('/change_status_plate/{id}', [PlateController::class, 'change_status_plate']);


// Routes for Godown
Route::post('/add_new_godown', [GodownController::class, 'add_new_godown']);
Route::post('/get_godowns', [GodownController::class, 'get_godowns']);

// Routes for Vendor
Route::post('/add_new_vendor', [VendorController::class, 'add_new_vendor']);
Route::post('/get_vendors', [VendorController::class, 'get_vendors']);
Route::post('/get_vendor_types', [VendorController::class, 'get_vendor_types']);
Route::post('/get_p_p_vendors', [VendorController::class, 'get_p_p_vendors']);
Route::post('/get_press_vendors', [VendorController::class, 'get_press_vendors']);
Route::post('/get_lamination_vendors', [VendorController::class, 'get_lamination_vendors']);
Route::post('/get_binder_vendors', [VendorController::class, 'get_binder_vendors']);

// Routes for Voucher
Route::post('/add_new_voucher/{vtype}', [VoucherController::class, 'add_new_voucher']);
Route::post('/get_processes_of_vendor_for_pv/{vid}', [VoucherController::class, 'get_processes_of_vendor_for_pv']);
Route::post('/add_new_voucher_press', [VoucherController::class, 'add_new_voucher_press']);
Route::post('/add_new_voucher_lamination', [VoucherController::class, 'add_new_voucher_lamination']);
Route::post('/get_purchase_vouchers/{vtype}', [VoucherController::class, 'get_purchase_vouchers']);
Route::post('/get_pv_voucher_data/{VoucherNo}', [VoucherController::class, 'get_pv_voucher_data']);
Route::put('/update_purchase_voucher', [VoucherController::class, 'update_purchase_voucher']);

// Route for Batch
Route::post('/get_processes', [BatchController::class, 'get_processes']);
Route::post('/add_new_batch', [BatchController::class, 'add_new_batch']);
Route::post('/get_batches_against_processes/{id}', [BatchController::class, 'get_batches_against_processes']);
Route::post('/get_batch_data_for_press/{batch}/{process}', [BatchController::class, 'get_batch_data_for_press']);
Route::post('/get_batch_data_for_lamination/{batch}/{process}/{recfrom}', [BatchController::class, 'get_batch_data_for_lamination']);
Route::post('/get_batch_data_for_binding/{batch}/{process}', [BatchController::class, 'get_batch_data_for_binding']);
Route::post('/get_batches_for_product_received/{vid}', [BatchController::class, 'get_batches_for_product_received']);
Route::post('/get_batch_data_for_book_received/{batch}', [BatchController::class, 'get_batch_data_for_book_received']);
Route::post('/get_batches_for_pv_against_vendor/{process}/{vid}', [BatchController::class, 'get_batches_for_pv_against_vendor']);
Route::post('/get_batch_data_for_press_pv/{batch}/{process}', [BatchController::class, 'get_batch_data_for_press_pv']);
Route::post('/get_batch_data_for_binder_pv/{batch}/{process}', [BatchController::class, 'get_batch_data_for_binder_pv']);
Route::post('/get_batches', [BatchController::class, 'get_batches']);
Route::post('/get_processes_of_batch/{batchno}', [BatchController::class, 'get_processes_of_batch']);
Route::post('/check_batch_isupdateable/{batchno}', [BatchController::class, 'check_batch_isupdateable']);
Route::put('/update_batch/{id}', [BatchController::class, 'update_batch']);
Route::put('/change_status_batch/{id}', [BatchController::class, 'change_status_batch']);


// Routes for PO Press
Route::post('/add_new_po_press', [POPressController::class, 'add_new_po_press']);

// Routes for PO Lamination
Route::post('/add_new_po_lamination', [POLaminationController::class, 'add_new_po_lamination']);

// Routes for PO Binding
Route::post('/add_new_po_binding', [POBinderController::class, 'add_new_po_binding']);

// Routes Book Receipt
Route::post('/add_new_book_received', [BookReceiptController::class, 'add_new_book_received']);


// Routes Account Voucher
Route::post('/get_accounts_by_type/{atype}', [AccountVoucherController::class, 'get_accounts_by_type']);
Route::post('/add_new_account_voucher', [AccountVoucherController::class, 'add_new_account_voucher']);

Route::group(['prefix' => 'auth'], function($router){
    Route::post('/register', [AuthController::class, 'register']);
    Route::any('/login', [AuthController::class, 'login'])->name("login");
    Route::any('/reset_password', [AuthController::class, 'reset_password']);
    Route::any('/change_password', [AuthController::class, 'change_password']);
});
//Route::group(['middleware' => ['auth:api', 'jwt.refresh']], function () {

Route::group(['middleware' => 'auth:api'], function($router){
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/profile_setting', [UserController::class, 'profile_setting']);
    Route::post('/get_products', [ProductController::class, 'get_products']);
});

}