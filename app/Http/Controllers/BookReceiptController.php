<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class BookReceiptController extends Controller{
    public function generateReceiptNo(){
        $currentDate = Date::now();
        $currentYear = $currentDate->format('Y');
        $currentMonth = $currentDate->format('m');

$count = DB::table("voucher_tbl")
->whereYear('created_date', '=', $currentYear)
->whereMonth('created_date', '=', $currentMonth)
->where('voucher_no', 'like', 'BR%')
->count();

$count = $count + 1;
$batchNo = "BR-" . $currentYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
return $batchNo;
    }

    public function add_new_book_received(Request $request){
        $reseiptNo = $this -> generateReceiptNo();
        

        $result = DB::table("voucher_tbl")->insert([
            "voucher_no" => $reseiptNo,
            "account_code" => $request -> Voucher['vendor_code'],
            "voucher_mode" =>  0,
            "gross_amount" => $request -> Voucher['total_amount'],
            "discount" => 0,
            "net_amount" => $request -> Voucher['total_amount'],
            "ref_no" => '',
            "created_date" => Date::now(),
            "created_by" => 1,
            "voucher_type" => 'Binder Receipt',
            
        ]);

        $inventories = $request -> inventories;

        foreach($inventories as $inventory){
            DB::table('inventory_tbl')->insert([
                "voucher_no" => $reseiptNo,
               "description" => $inventory['product_id'],
               "qtyin" => $inventory['qty'],
               "qtyout" => 0,
               "godown" => $inventory['godown_id'],
               "batch_no" => $inventory['batch_no'],
               "rate" => $inventory['product_rate'],
               "amount" => $inventory['product_amount'],

            ]);

        }
    }
}