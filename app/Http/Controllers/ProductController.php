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
                ->leftJoin("category_tbl", "category_tbl.id", "product_tbl.category")
                ->orderBy("id", "DESC")
                ->select("product_tbl.*", "category_tbl.category as category_name")
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
    $searchTerm = $request->search_term;

        $products = DB::table("product_tbl")
            ->leftJoin("category_tbl", "category_tbl.id", "product_tbl.category")
            ->leftJoin("subject_tbl", "subject_tbl.id", "product_tbl.subject")
            ->leftJoin("book_for_board_tbl", "book_for_board_tbl.id", "product_tbl.book_for")
            ->leftJoin("sheet_size_tbl as book_sheet", "book_sheet.id", "product_tbl.book_sheet_size")
            ->leftJoin("sheet_size_tbl as title_sheet", "title_sheet.id", "product_tbl.title_sheet_size")
            ->orderBy("id", "DESC")
            ->where(function ($query) use ($request) {
                if($request->search_term !== ""){
                    $query->where('product_tbl.product_code', 'LIKE', '%' . $request->search_term . '%')
                        ->orWhere('product_tbl.product_name', 'LIKE', '%' . $request->search_term . '%')
                        ->orWhere('product_tbl.face_price', 'LIKE', '%' . $request->search_term . '%')
                        ->orWhere('product_tbl.weight', 'LIKE', '%' . $request->search_term . '%')
                        ->orWhere('book_sheet.sheet', 'LIKE', '%' . $request->search_term . '%')
                        ->orWhere('title_sheet.sheet', 'LIKE', '%' . $request->search_term . '%')
                        ->orWhere('category_tbl.category', 'LIKE', '%' . $request->search_term . '%')
                        ->orWhere('subject_tbl.subject', 'LIKE', '%' . $request->search_term . '%')
                        ->orWhere('product_tbl.product_sname', 'LIKE', '%' . $request->search_term . '%');
                }
            })
            ->select(
                "product_tbl.*",
                "category_tbl.category as category_name",
                "subject_tbl.subject as subject_name",
                "book_for_board_tbl.name as board_name",
                "book_sheet.sheet as book_sheet_size_label",
                "title_sheet.sheet as title_sheet_size_label"
            )
            ->get();

        return response()->json([
            "success" => true,
            "products" => $products
        ]);
    }

    public function get_p_f_plates(Request $request){

        $products = DB::table("product_for_plates_view")
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

    public function update_product(Request $request, $id){
        $result = DB::table("product_tbl")->where("id", $id)->update([
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
                ->leftJoin("category_tbl", "category_tbl.id", "product_tbl.category")
                ->orderBy("id", "DESC")
                ->select("product_tbl.*", "category_tbl.category as category_name")
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

    public function change_status_product(Request $request, $id){
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
