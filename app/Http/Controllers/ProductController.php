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
            "inner_pages" => $request->inner_pages,
            "rule_pages" => $request->rule_pages,
            "amount_of_farmay" => $request->farmay,
            "weight" => $request->book_weight,
            "subject" => $request->subject,
            "book_for" => $request->book_for,
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

    public function get_category(Request $request){
        $category = DB::table("category_tbl")
        ->orderBy("id", "DESC")
        ->get();

        return response()->json([
            "Success" => true,
            "category" => $category
        ]);
    }

    public function get_subjects(Request $request){
        $subject = DB::table("subject_tbl")
        ->orderBy("id", "DESC")
        ->get();

        return response()->json([
            "Success" => true,
            "subject" => $subject
        ]);
    }

    public function get_sheet_sizes(Request $request){
        $sheets = DB::table("sheet_size_tbl")
        ->orderBy("id", "DESC")
        ->get();

        return response()->json([
            "Success" => true,
            "sheets" => $sheets
        ]);
    }

    public function get_book_for_board(Request $request){
        $boards = DB::table("book_for_board_tbl")
        ->orderBy("id", "DESC")
        ->get();

        return response()->json([
            "Success" => true,
            "boards" => $boards
        ]);

    }

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

    public function add_new_subject(Request $request){
        $result = DB::table("subject_tbl")->insert([
            "subject" => $request -> subject,
        ]);
        if ($result == 1 || $result == 0){
            $subjects = DB::table("subject_tbl")
                ->orderBy("id", "DESC")
                ->get();
            return response()->json([
                "success" => 1,
                "subjects" => $subjects
            ]);
        }else{
            return response()->json([
                "success" => 0
            ]);
        }
    }
    
}
