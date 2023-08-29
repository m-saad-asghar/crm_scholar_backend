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
        "voucher_type" => 'Purchase Order Press',
       ]);
       


$inventories = $request -> inventories;
       foreach($inventories as $inventory ){
        
        $updateBatch = DB::table('batch_history_tbl')
        ->where('batch_no', $inventory['batch_no'])
        ->where('process', $inventory['process_id'])
        ->update([
            'voucher_no' => $Voucher,
            'process_date' => Date::now(),
            'active' => 1,
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
       $posResponse = $this -> get_pos($request, 'Purchase Order Press');
       if ($posResponse->getStatusCode() == 200) 
        $latestData = json_decode($posResponse->getContent(), true);
        

       return response()->json([
        'success' => 1,
        'pos' => $latestData['pos'],

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

public function get_pos(Request $request, $potype){
    $orders = DB::table('voucher_tbl as vt')
    ->leftjoin('chart_of_account_tbl as coa', 'vt.account_code', '=', 'coa.code')
    ->select('vt.id', 'vt.voucher_no', 'coa.account_name', 'vt.created_date', 'vt.active', 'vt.account_code')
    ->where('vt.voucher_type', '=', $potype)
    ->orderby('vt.id', 'DESC')
    ->get();

    return response()->json([
        'success' => 1,
        'pos' => $orders

       ]);

}
//------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------

public function change_status_po(Request $request, $id){
    $result = DB::table('voucher_tbl')
    ->where('id', '=', $id)
    ->update(["active" => $request->status,]);

    if ($result == 1){
        return response()->json([
            "success" => 1
        ]);
    }else{
        return response()->json([
            "success" => 0
        ]);
    }
}
//------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------
public function get_po_press_detail(Request $request, $voucherno){
    $result = DB::table('po_press_detail_view')
    ->where('voucher_no', '=', $voucherno)
    ->get();

    return response()->json([
        "data" => $result,
        "success" => 1,
    ]);

}
//------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------
public function update_po_press(Request $request){
    DB::beginTransaction();
    try{
        $result = DB::table('voucher_tbl')
        ->where('voucher_no', '=', $request -> Voucher['voucher'])
        ->update([
           
            "gross_amount" => $request -> Voucher['total_amount'],
           
            "net_amount" => $request -> Voucher['total_amount'],
            
           ]);
    
           // Delete Inventories
           $inventories = $request -> inventories;
           foreach($inventories['deletedEntries'] as $inventory){
               $deleteBHT = DB::table(('batch_history_tbl'))
           ->where('batch_no', '=', $inventory['batch_no'])
           ->where('process', '=', $inventory['process_id'])
           ->update([
               'voucher_no' => null,
               
           ]);
    
           $deletetempinventories = DB::table('temp_inventory_tbl')
           ->where('batch_no', '=', $inventory['batch_no'])
           ->where('process', '=', $inventory['process_id'])
           ->delete();
    
           $deleteinventories = DB::table('inventory_tbl')
           ->where('batch_no', '=', $inventory['batch_no'])
           ->where('process', '=', $inventory['process_id'])
           ->delete();
    
    
       }
       // Insert Inventories
       $inventories = $request -> inventories;
       foreach($inventories['insertedEntries'] as $inventory){
           $deleteInventories = DB::table(('batch_history_tbl'))
       ->where('batch_no', '=', $inventory['batch_no'])
       ->where('process', '=', $inventory['process_id'])
       ->update([
        'voucher_no' => $request -> Voucher['voucher'],
        'process_date' => Date::now(),
        'active' => 1,
           
       ]);
    
       $inserTempInventory = DB::table('temp_inventory_tbl')->insert([
        'batch_no' => $inventory['batch_no'],
        'voucher_no' => $request -> Voucher['voucher'],
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
        'voucher_no' => $request -> Voucher['voucher'],
        'description' => $inventory['paper_product_id'],
        'qtyin' => 0,
        'qtyout' => $inventory['paper_qty'],
        'godown' => $inventory['godown_id'],
        'rate' => $inventory['product_rate'],
        'amount' => $inventory['product_amount'],
    
    ]);
    }

    DB::commit();
    $posResponse = $this -> get_pos($request, 'Purchase Order Press');
    if ($posResponse->getStatusCode() == 200) 
     $latestData = json_decode($posResponse->getContent(), true);
     
     return response()->json([
        'success' => 1,
        'pos' => $latestData['pos'],
    
       ]);

    }
    catch(Exception $ex){
        DB::rollback();
        return response()->json([
            'success' => 0,
            
        
           ]);
    }
    
}
}