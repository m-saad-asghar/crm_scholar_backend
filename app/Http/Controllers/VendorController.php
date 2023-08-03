<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class VendorController extends Controller{

    public function add_new_vendor(Request $request){

$code = DB::table("chart_of_account_tbl")
->where('code', 'like', '02-01%')
->count();

$code = $code + 1;
$acode = "02-01-" . str_pad($code, 3, '0', STR_PAD_LEFT);


    DB::beginTransaction();
    try{

        $resultcoa = DB::table("chart_of_account_tbl")->insert([
            "code" => $acode,
            "account_name" => $request -> name,
            "description" =>  '',
            "opening_balance" => 0,
            "openingbalance_type" => 0,
            "is_parent" => 0,
            "increase_account" => 0,
            "statement_item" => 'Income Statement',
            
        ]);

        $result = DB::table("vendor_tbl")->insert([
            "code" => $acode,
            "name" => $request -> name,
            "address" => $request -> address,
            "contact_no" => $request -> contact_no,
            
        ]);
       // $vendorTypes = json_decode($request->vendor_type, true);
        
        $vendorTypes = $request -> vendor_type;

        foreach($vendorTypes as $vtype){
            DB::table('vendor_type_detail_tbl')->insert([
                "vendor" => $acode,
               "vendor_type" => $vtype['id'], 
            ]);

        }
    
        DB::commit();
        
    
        if ($result == 1 || $result == 0){
            $vendors = DB::table("vendor_tbl")
                ->orderBy("id", "DESC")
                ->get();
            return response()->json([
                "success" => 1,
                "vendors" => $vendors
            ]);
        }else{
            return response()->json([
                "success" => 0
            ]);
        }

    } 
        catch(Exception $e){
            DB::rollback();
            throw $e;
            return response()->json([
                "success" => 0
            ]);
            
        }

    }

    public function get_vendors(Request $request){
        
        $vendors = DB::table("vendor_tbl")
        ->orderBy("id", "DESC")
        ->get();

        return response()->json([
            "success" => true,
            "vendors" => $vendors
        ]); 
       
    }
    public function get_p_p_vendors(Request $request){
        $vendors = DB::table('paper_and_plate_vendor_view')
        ->orderBy("code", "DESC")
        ->get();

        return response()->json([
            "success" => true,
            "vendors" => $vendors
        ]); 
    }
    public function get_press_vendors(Request $request){
        $vendors = DB::table('press_vendor_view')
        ->orderBy("code", "DESC")
        ->get();

        return response()->json([
            "success" => true,
            "vendors" => $vendors
        ]); 
    }
    public function get_lamination_vendors(Request $request){
        $vendors = DB::table('lamination_vendor_view')
        ->orderBy("code", "DESC")
        ->get();

        return response()->json([
            "success" => true,
            "vendors" => $vendors
        ]); 
    }
    public function get_binder_vendors(Request $request){
        $vendors = DB::table('binder_vendor_view')
        ->orderBy("code", "DESC")
        ->get();

        return response()->json([
            "success" => true,
            "vendors" => $vendors
        ]); 
    }
    public function get_vendor_types(Request $request){
        
        $vendor_types = DB::table("vendor_type_tbl")
        ->orderBy("id", "DESC")
        ->get();

        return response()->json([
            "success" => true,
            "vendor_types" => $vendor_types
        ]); 
       
    }
}


