<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function add_new_product(Request $request){
        $result = DB::table("product_tbl")->insert([
            "product_code" => $request->product_bar_code,
            "product_sname" => $request->product_short_name,
            "product_name" => $request->product_name,
            "face_price" => $request->face_price,
            "pages" => $request->pages,
            "inner_pages" => $request->farmay,
            "rule_pages" => $request->rule_pages,
            "book_sheet_size" => $request->sheet_size,
            "title_sheet_size" => $request->title_sheet_size,
            "category" => $request->category,
        ]);
        if ($result == 1 || $result == 0){
            $products = DB::table("product_tbl")
                ->where("active", 1)
                ->orderBy("id", "DESC")
                ->get();
            return response()->json([
                "success" => 1,
                "products" => $products
            ]);
        }else{
            return response()->json([
                "success" => 0
            ]);
        }
    }

    public function get_products(Request $request){
        $products = DB::table("product_tbl")
            ->where("active", 1)
            ->orderBy("id", "DESC")
            ->get();
        return response()->json([
            "success" => true,
            "products" => $products
        ]);
    }
}
