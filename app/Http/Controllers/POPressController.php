<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class POPressController extends Controller{

    public function generateVoucher(){
        $currentDate = Date::now();
        $currentYear = $currentDate->format('Y');
        $currentMonth = $currentDate->format('m');

$count = DB::table("voucher_tbl")
->where('voucher_no', 'like', 'PO-%')
->whereYear('created_date', '=', $currentYear)
->whereMonth('created_date', '=', $currentMonth)
->count();

$count = $count + 1;
$voucher = "PO-" . $currentYear . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . str_pad($count, 4, '0', STR_PAD_LEFT);

return $voucher;
    }

    public function add_new_po_press(Request $request){
       $Voucher = generateVoucher();
$inventories = $request -> inventories;
       foreach($inventories as $inventory ){
        
        $updateBatch = DB::table('batch_history_tbl')
        ->where('batch_no', $inventory -> batch_no)
        ->where('process', $inventory -> process_id)
        ->update([
            'voucher_no' => $Voucher,
            'process_date' => Date::now(),
        ]);

        $inserTempInventory = DB::table('temp_service_inventory_tbl')->insert([
            'batch_no' => $inventory -> batch_no,
            'voucher_no' => $Voucher,
            'plates' => $inventory -> plates_qty,
            'qty' => $inventory -> print_order,
            'rate' => $inventory -> product_rate,
            'amount' => $inventory -> product_amount,
            'process' => $inventory -> process_id,
            
        ]);

      //  $insertPaperInventory = DB::table('inventory_tbl')->insert([
           
       // ])

       }
       


    }
}