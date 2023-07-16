<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaperTypeController extends Controller{

   /* public function add_new_paper_type(Request $request){
        $result = DB::table("paper_type_tbl")->insert([
            "paper_type" => $request -> paper_type,
        ]);
        if ($result == 1 || $result == 0){
            $paper_types = DB::table("paper_type_tbl")
                ->orderBy("id", "DESC")
                ->get();
            return response()->json([
                "success" => 1,
                "paper_types" => $paper_types
            ]);
        }else{
            return response()->json([
                "success" => 0
            ]);
        }
    } */

public function add_new_paper_type(Request $request){
    $result = DB::table("product_child_type_tbl")->insert([
         "parent_type" => 2,
         "child_type" => $request -> paper_type,
    ]);
    if ($result == 1 || $result == 0){
        $paper_types = DB::table("product_child_type_tbl")
        ->where("parent_type", "=", 2)
            ->orderBy("id", "DESC")
            ->get();
        return response()->json([
            "success" => 1,
            "paper_types" => $paper_types
        ]);
    }else{
        return response()->json([
            "success" => 0
        ]);
    }
}
    public function get_paper_types(Request $request){
        $paper_types = DB::table("product_child_type_tbl")
        ->where("parent_type", "=", 2)
            ->orderBy("id", "DESC")
            ->get();

        return response()->json([
            "success" => true,
            "paper_types" => $paper_types
        ]);
    }
}


