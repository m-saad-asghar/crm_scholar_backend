<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Exception;

class VoucherController extends Controller{

    public function generateVoucher(){
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

return $voucher;
    }


    public function add_new_voucher(Request $request, $vtype){

        $currentDate = Date::now();
       

$voucher = $this -> generateVoucher();

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
        "voucher_type" => $vtype,
        
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
               "amount" => $inventory['product_amount'],
               "product_for" => $inventory['product_for'],

            ]);

        }

    DB::commit();

    $vouchers = DB::table('voucher_tbl as vt')
        ->leftJoin('chart_of_account_tbl as coa', 'vt.account_code', '=', 'coa.code')
        ->select('vt.id', 'vt.voucher_no', 'coa.account_name', 'vt.gross_amount', 'vt.discount', 'vt.net_amount', 'vt.created_date')
        ->where('vt.voucher_type', '=', $vtype)
        ->get();

    return response()->json([
        "success" => 1,
        "vouchers" => $vouchers,
        
    ]);
}
catch(Exception $e){
DB::rollback();

return response()->json([
    "success" => 0,
    
    
]);
} 

        
    }

    public function get_processes_of_vendor_for_pv(Request $request, $vid){
        $query = DB::table('batch_history_tbl as bht')
    ->select('bpt.id', 'bpt.process')
    ->leftJoin('batch_process_tbl as bpt', 'bht.process', '=', 'bpt.id')
    ->leftJoin('voucher_tbl as vt', 'bht.voucher_no', '=', 'vt.voucher_no')
    ->where('vt.account_code', '=', $vid)
    ->where('bht.isbilled', '=', 0)
    ->distinct()
    ->get();

    if($query -> count() > 0){
        return response()->json([
            "success" => 1,
            "processes" => $query,
        ]);
    }
    else{
        return response()->json([
            "success" => 0,
        ]);
    }

    }

    public function add_new_voucher_press(Request $request){

        $currentDate = Date::now();       

$voucher = $this -> generateVoucher();

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
        "remarks" => 'Purchase of Printing Press Services',

    ]);

    $register1 = DB::table('account_register_tbl')->insert([
        "account_code" => '05-01-001',
        "voucher_no" => $voucher,
        "dr" => $request -> Voucher['total_amount'],
        "cr" => 0,
        "remarks" => 'Purchase of Printing Press Services',

    ]);
    
    $inventories = $request -> inventories;

        foreach($inventories as $inventory){
            DB::table('inventory_tbl')->insert([
                "voucher_no" => $voucher,
               "description" => $inventory['product_id'],
               "qtyin" => $inventory['print_qty'],
               "qtyout" => 0,
               
               "batch_no" => $inventory['batch_no'],
               "process" => $inventory['process_id'],
               "rate" => $inventory['product_rate'],
               "amount" => $inventory['product_amount'],

            ]);

            $updateBatchHistory = DB::table('batch_history_tbl')
            ->where('batch_no', '=', $inventory['batch_no'])
            ->where('process', '=', $inventory['process_id'])
            ->update([
            'isbilled' => 1,
            'pv_no' => $voucher,
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
public function add_new_voucher_lamination(Request $request){

        $currentDate = Date::now();       

$voucher = $this -> generateVoucher();

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
        "remarks" => 'Purchase of Printing Press Services',

    ]);

    $register1 = DB::table('account_register_tbl')->insert([
        "account_code" => '05-01-001',
        "voucher_no" => $voucher,
        "dr" => $request -> Voucher['total_amount'],
        "cr" => 0,
        "remarks" => 'Purchase of Printing Press Services',

    ]);
    
    $inventories = $request -> inventories;

        foreach($inventories as $inventory){
            DB::table('inventory_tbl')->insert([
                "voucher_no" => $voucher,
               "description" => $inventory['product_id'],
               "qtyin" => $inventory['received_qty'],
               "qtyout" => 0,
               
               "batch_no" => $inventory['batch_no'],
               "process" => $inventory['process_id'],
               "rate" => $inventory['product_rate'],
               "amount" => $inventory['product_amount'],

            ]);

            $updateBatchHistory = DB::table('batch_history_tbl')
            ->where('batch_no', '=', $inventory['batch_no'])
            ->where('process', '=', $inventory['process_id'])
            ->update([
            'isbilled' => 1,
            'pv_no' => $voucher,
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

    public function get_purchase_vouchers(Request $request, $vtype){
        $vouchers = DB::table('voucher_tbl as vt')
        ->leftJoin('chart_of_account_tbl as coa', 'vt.account_code', '=', 'coa.code')
        ->select('vt.id', 'vt.voucher_no', 'coa.account_name', 'vt.gross_amount', 'vt.discount', 'vt.net_amount', 'vt.created_date')
        ->where('vt.voucher_type', '=', $vtype)
        ->get();

        
            return response()->json([
                "success" => 1,
                "vouchers" => $vouchers,
                
            ]);
        
        
    }
    public function update_purchase_voucher(Request $request){

        $updateVoucher = DB::table('voucher_tbl')
        ->where('voucher_no', '=', $request -> Voucher['voucher_no'])
        ->update([
        
        "gross_amount" => $request -> Voucher['total_amount'],
        "discount" => 0,
        "net_amount" => $request -> Voucher['total_amount'],
        
        ]);

        $register = DB::table('account_register_tbl')
        ->where("account_code" ,'=', $request -> Voucher['vendor_code'])
        ->where("voucher_no" ,'=', $request -> Voucher['voucher_no'])
        ->update([
            
            "cr" => $request -> Voucher['total_amount'],
            
    
        ]);
    
        $register1 = DB::table('account_register_tbl')
        ->where("account_code" ,'=', '05-01-001')
        ->where("voucher_no" ,'=', $request -> Voucher['voucher_no'])
        ->update([
            
            "dr" => $request -> Voucher['total_amount'],
           
    
        ]);

        // Delete Inventories
        $inventories = $request -> inventories;
        foreach($inventories['deletedEntries'] as $inventory){
            $deleteInventories = DB::table(('inventory_tbl'))
        ->where('id', '=', $inventory['inventory_id'])
        ->update([
            'active' => 0
        ]);
    }
        // Update Inventories
        foreach($inventories['updatedEntries'] as $inventory){
            $deleteInventories = DB::table(('inventory_tbl'))
        ->where('id', '=', $inventory['inventory_id'])
        ->update([
            'qtyin' => $inventory['product_qty'],
            'rate' => $inventory['product_rate'],
            'amount' => $inventory['product_amount'],
            "product_for" => $inventory['product_for'],
        ]);
    }

        // Inserted Inventories
        foreach($inventories['insertedEntries'] as $inventory){
            DB::table('inventory_tbl')->insert([
                "voucher_no" => $request -> Voucher['voucher_no'],
               "description" => $inventory['product_id'],
               "qtyin" => $inventory['product_qty'],
               "qtyout" => 0,
               "godown" => $inventory['godown_id'],
               "batch_no" => '',
               "rate" => $inventory['product_rate'],
               "amount" => $inventory['product_amount'],
               "product_for" => $inventory['product_for'],

            ]);

    }

    $vouchers = DB::table('voucher_tbl as vt')
    ->leftJoin('chart_of_account_tbl as coa', 'vt.account_code', '=', 'coa.code')
    ->select('vt.id', 'vt.voucher_no', 'coa.account_name', 'vt.gross_amount', 'vt.discount', 'vt.net_amount', 'vt.created_date')
    ->where('vt.voucher_type', '=', 'Purchase Voucher PP')
    ->get();

    
        return response()->json([
            "success" => 1,
            "vouchers" => $vouchers,
            
        ]);
    
        
    }

    public function get_pv_voucher_data(Request $request, $VoucherNo){
       

        $inventories = DB::table('inventory_tbl as it')
        ->leftJoin('product_tbl as pt', 'it.description', '=', 'pt.id')
        ->leftJoin('godown_tbl as gt', 'it.godown', '=', 'gt.id')
        ->leftJoin('product_tbl as pt1', 'it.product_for', '=', 'pt1.id')
        ->where('voucher_no', '=', $VoucherNo)
        ->where('it.active', '=', 1)
        ->select('it.*', 'pt.product_name', 'gt.name as godownName', 'pt1.product_name as productFor')
        ->get();

        
        if($inventories -> count() > 0){
            return response()->json([
                "success" => 1,
                "inventories" => $inventories,
                
            ]);
        }
        else{
            return response()->json([
                "success" => 0,
                
                
            ]);
        }
    }

    }

    



