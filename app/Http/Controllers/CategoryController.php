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
                "categories" => $categories
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
}


