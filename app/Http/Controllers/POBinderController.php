<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Exception;

class POBinderController extends Controller{

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

    public function add_new_po_binding(Request $request){
       $Voucher = $this -> generateVoucher();
       DB::beginTransaction();
try{


       $result = DB::table('voucher_tbl')->insert([
        "voucher_no" => $Voucher,
        "account_code" => $request -> Voucher['vendor_code'],
        "voucher_mode" =>  0,
        "gross_amount" => $request -> Voucher['total_amount'],
        "discount" => 0,
        "net_amount" => $request -> Voucher['total_amount'],
        "ref_no" => '',
        "created_date" => Date::now(),
        "created_by" => 1,
        "voucher_type" => 'Purchase Order',
       ]);
       


$inventories = $request -> inventories;
       foreach($inventories as $inventory ){
        
        $updateBatch = DB::table('batch_history_tbl')
        ->where('batch_no', $inventory['batch_no'])
        ->where('process', $inventory['process_id'])
        ->update([
            'voucher_no' => $Voucher,
            'received_from' => $inventory['pickup_location_id'],
            'process_date' => Date::now(),
        ]);

        $inserTempInventory = DB::table('temp_inventory_tbl')->insert([
            'batch_no' => $inventory['batch_no'],
            'voucher_no' => $Voucher,
            
            'qty' => $inventory['print_order'],
            'rate' => $inventory['product_rate'],
            'amount' => $inventory['product_amount'],
            'process' => $inventory['process_id'],
            'pickup_location' => $inventory['pickup_location_id'],
            
            
        ]);

       

       }

       DB::commit();

       return response()->json([
        'success' => 1,

       ]);
       
   }
    catch(Exception $e){
        DB::rollback();
        throw $e;
        return response()->json([
            'success' => 0,
            
           ]);
    }


    }
}