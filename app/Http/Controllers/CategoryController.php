<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller{

    public function add_new_category(Request $request){
        $result = DB::table("category_tbl")->insert([
            "category" => $request -> category,
        ]);
        if ($result == 1 || $result == 0){
            $categories = DB::table("category_tbl")
                ->orderBy("id", "DESC")
                ->get();
            return response()->json([
                "success" => 1,
                "category" => $categories
            ]);
        }else{
            return response()->json([
                "success" => 0
            ]);
        }
    }

    public function get_category(Request $request){
        $category = DB::table("category_tbl")
        ->orderBy("id", "DESC")
        ->get();

        return response()->json([
            "success" => true,
            "category" => $category
        ]);
    }
    public function update_category(Request $request, $id){
        $update = DB::table(('category_tbl'))
        ->where('id', '=', $id)
        ->update([
            "category" => $request -> category, 
        ]);

        if($update === 1){
            $category = DB::table("category_tbl")
            ->orderBy("id", "DESC")
            ->get();
    
            return response()->json([
                "success" => 1,
                "category" => $category
            ]);
        }
        else{
            return response()->json([
                "success" => 0,
                
            ]);
        }
        
        
    }
    public function change_status_category(Request $request, $id){
        $result = DB::table("category_tbl")->where("id", $id)->update([
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


