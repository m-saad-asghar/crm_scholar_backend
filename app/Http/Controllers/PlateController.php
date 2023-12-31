<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlateController extends Controller{

    public function add_new_plate(Request $request){
        $result = DB::table("product_tbl")->insert([

            "product_code" => $request-> plate,
            "product_sname" => $request-> plate,
            "product_name" => $request-> plate,
            "face_price" => 0,
            "pages" => 0,
            "inner_pages" => 0,
            "rule_pages" => 0,
            "farmay" => 0,
            "weight" => 0,
            "subject" => 0,
            "book_for" => 0,
            "book_sheet_size" => 0,
            "title_sheet_size" => 0,
            "category" => 0,
            "length" => 0,
            "width" => 0,
            "product_type" => 3,
            "child_type" => 2,

        ]);
        if ($result == 1 || $result == 0){
            $plates = DB::table("product_tbl")
            
            ->select('id', 'product_name as plate', 'active')
            ->where('product_type', '=', 3)    
            ->orderBy("id", "DESC")
                ->get();
            return response()->json([
                "success" => 1,
                "plates" => $plates
            ]);
        }else{
            return response()->json([
                "success" => 0
            ]);
        }
    }

    public function get_plates(Request $request){
        $plates = DB::table("product_tbl")
            ->select('id', 'product_name as plate', 'active')
            ->where('product_tbl.product_type', '=', 3)    
            ->orderBy("id", "DESC")
                ->get();
            return response()->json([
                "success" => 1,
                "plates" => $plates
            ]);
        
    }
    public function get_plate_with_id(Request $request){
        $plates = DB::table("product_tbl")
        ->select('id', 'product_name as plate')
        ->where('product_type', '=', 3)
        ->orderBy('id', 'DESC')
        ->get();

        return response()->json([
            "success" => true,
            "plates" => $plates
        ]);
    }
    public function update_plate(Request $request, $id){
        $update = DB::table(('product_tbl'))
        ->where('id', '=', $id)
        ->update([
            "product_code" => $request-> plate,
            "product_sname" => $request-> plate,
            "product_name" => $request-> plate,
        ]);

        if($update === 1){
            $plates = DB::table("product_tbl")
            ->select('id', 'product_name as plate', 'active')
            ->where('product_tbl.product_type', '=', 3)    
            ->orderBy("id", "DESC")
                ->get();
    
            return response()->json([
                "success" => 1,
                "plates" => $plates
            ]);
        }
        else{
            return response()->json([
                "success" => 0,
                
            ]);
        }
        
        
    }
    public function change_status_plate(Request $request, $id){
        $result = DB::table("product_tbl")->where("id", $id)->update([
            "active" => ($request->status == true) ? 1 : 0,
        ]);
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
}


