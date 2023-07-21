<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Exception;

class VoucherController extends Controller{
/*
    public function add_new_voucher(Request $request){
        
        $currentDate = Date::now();
        $currentYear = $currentDate->format('Y');
        $currentMonth = $currentDate->format('m');

$count = DB::table("voucher_tbl")
->where('voucher_no', 'like', 'PV-%')
->whereYear('created_date', '=', $currentYear)
->whereMonth('created_date', '=', $currentMonth)
->count();

$count = $count + 1;
$voucher = "PV-" . $currentYear . "-" . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . "-" . str_pad($count, 4, '0', STR_PAD_LEFT);


    DB::beginTransaction();
    try{

        $result = DB::table("voucher_tbl")->insert([
            "voucher_no" => $voucher,
            "account_code" => $request -> Voucher -> account_code,
            "voucher_mode" =>  0,
            "gross_amount" => $request -> Voucher -> total_amount,
            "discount" => 0,
            "net_amount" => $request -> Voucher -> total_amount,
            "ref_no" => '',
            "created_date" => $currentDate,
            "created_by" => 1,
            "voucher_type" => 'Purchase Voucher',
            
        ]);

        
        
        $inventories = $request -> inventories;

        foreach($inventories as $inventory){
            DB::table('inventory_tbl')->insert([
                "voucher_no" => $voucher,
               "description" => $inventory -> product_id,
               "qtyin" => $inventory -> product_qty,
               "qtyout" => 0,
               "godown" => $inventory -> godown_id,
               "for_product" => '',
               "batch_no" => '',
               "rate" => $inventory -> product_rate,

            ]);

        }

    
        DB::commit();
        
        return response()->json([
            "success" => 1
    
        ]);

    } 
        catch(Exception $e){
            DB::rollback();
            throw $e;
            return response()->json([
                "success" => 0
            ]);
            
        }

    }
    */

    public function add_new_voucher(Request $request){

        $currentDate = Date::now();
        $currentYear = $currentDate->format('Y');
        $currentMonth = $currentDate->format('m');

$count = DB::table("voucher_tbl")
->where('voucher_no', 'like', 'PV-%')
->whereYear('created_date', '=', $currentYear)
->whereMonth('created_date', '=', $currentMonth)
->count();

$count = $count + 1;
$voucher = "PV-" . $currentYear . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . str_pad($count, 4, '0', STR_PAD_LEFT);

DB::beginTransaction();
try{

    $result = DB::table("voucher_tbl")->insert([
        "voucher_no" => $voucher,
        "account_code" => $request -> Voucher['vendor_code'],
        "voucher_mode" =>  0,
        "gross_amount" => $request -> Voucher['total_amount'],
        "discount" => 0,
        "net_amount" => $request -> Voucher['total_amount'],
        "ref_no" => '',
        "created_date" => $currentDate,
        "created_by" => 1,
        "voucher_type" => 'Purchase Voucher',
        
    ]);
    

    $register = DB::table('account_register_tbl')->insert([
        "account_code" => $request -> Voucher['vendor_code'],
        "voucher_no" => $voucher,
        "dr" => 0,
        "cr" => $request -> Voucher['total_amount'],
        "remarks" => 'Purchase of Paper and Plates',

    ]);

    $register1 = DB::table('account_register_tbl')->insert([
        "account_code" => '05-01-001',
        "voucher_no" => $voucher,
        "dr" => $request -> Voucher['total_amount'],
        "cr" => 0,
        "remarks" => 'Purchase of Paper and Plates',

    ]);
    
    $inventories = $request -> inventories;

        foreach($inventories as $inventory){
            DB::table('inventory_tbl')->insert([
                "voucher_no" => $voucher,
               "description" => $inventory['product_id'],
               "qtyin" => $inventory['product_qty'],
               "qtyout" => 0,
               "godown" => $inventory['godown_id'],
               "batch_no" => '',
               "rate" => $inventory['product_rate'],
               "product_for" => $inventory['product_for'],

            ]);

        }

    DB::commit();

    return response()->json([
        "success" => 1,
        "voucher" => $voucher,
        
    ]);
}
catch(Exception $e){
DB::rollback();

return response()->json([
    "success" => 0,
    "voucher" => $voucher,
    
]);
}

        
    }

    }

    



