<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SheetSizeController extends Controller{

    public function add_new_sheet_size(Request $request){
        $result = DB::table("sheet_size_tbl")->insert([
            "sheet" => $request -> sheet,
            "length" => $request -> length,
            "width" => $request -> width,
            "portion" => $request -> portion,

        ]);
        if ($result == 1 || $result == 0){
            $sheets = DB::table("sheet_size_tbl")
                ->orderBy("id", "DESC")
                ->get();
            return response()->json([
                "success" => 1,
                "sheets" => $sheets
            ]);
        }else{
            return response()->json([
                "success" => 0
            ]);
        }
    }

    public function get_sheet_sizes(Request $request){
        $sheets = DB::table("sheet_size_tbl")
        ->orderBy("id", "DESC")
        ->get();

        return response()->json([
            "success" => true,
            "sheets" => $sheets
        ]);
    }
    public function update_sheet_size(Request $request, $id){
        $update = DB::table(('sheet_size_tbl'))
        ->where('id', '=', $id)
        ->update([
            "sheet" => $request -> sheet,
            "length" => $request -> length,
            "width" => $request -> width,
            "portion" => $request -> portion,
        ]);

        if($update === 1){
            $sheets = DB::table("sheet_size_tbl")
            ->orderBy("id", "DESC")
            ->get();
    
            return response()->json([
                "success" => 1,
                "sheets" => $sheets
            ]);
        }
        else{
            return response()->json([
                "success" => 0,
                
            ]);
        }
        
        
    }
    public function change_status_sheet_size(Request $request, $id){
        $result = DB::table("sheet_size_tbl")->where("id", $id)->update([
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


