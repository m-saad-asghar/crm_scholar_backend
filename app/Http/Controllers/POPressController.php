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
//------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------
    public function add_new_po_press(Request $request){
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
            'process_date' => Date::now(),
        ]);

        $inserTempInventory = DB::table('temp_inventory_tbl')->insert([
            'batch_no' => $inventory['batch_no'],
            'voucher_no' => $Voucher,
            'plates' => $inventory['plates_qty'],
            'qty' => $inventory['print_order'],
            'rate' => $inventory['product_rate'],
            'amount' => $inventory['product_amount'],
            'process' => $inventory['process_id'],
            'godown' => $inventory['godown_id'],
            
        ]);

        $insertPaperInventory = DB::table('inventory_tbl')->insert([
            'batch_no' => $inventory['batch_no'],
            'process' => $inventory['process_id'],
            'voucher_no' => $Voucher,
            'description' => $inventory['paper_product_id'],
            'qtyin' => 0,
            'qtyout' => $inventory['paper_qty'],
            'godown' => $inventory['godown_id'],
            'rate' => $inventory['product_rate'],
            'amount' => $inventory['product_amount'],

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
//------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------

public function get_pos(Request $request){
    $orders = DB::table('voucher_tbl as vt')
    ->leftjoin('chart_of_account_tbl as coa', 'vt.account_code', '=', 'coa.code')
    ->select('vt.voucher_no', 'coa.account_name', 'vt.created_date')
    ->get();

    return response()->json([
        'success' => 1,
        'pos' => $orders

       ]);

}
}