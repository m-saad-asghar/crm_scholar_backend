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
            "farmay" => $request->farmay,
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
                ->leftJoin("subject_tbl", "subject_tbl.id", "product_tbl.subject")
                ->leftJoin("book_for_board_tbl", "book_for_board_tbl.id", "product_tbl.book_for")
                ->leftJoin("sheet_size_tbl as book_sheet", "book_sheet.id", "product_tbl.book_sheet_size")
                ->leftJoin("sheet_size_tbl as title_sheet", "title_sheet.id", "product_tbl.title_sheet_size")
                ->orderBy("id", "DESC")
                ->select(
                    "product_tbl.*",
                    "category_tbl.category as category_name",
                    "subject_tbl.subject as subject_name",
                    "book_for_board_tbl.name as board_name",
                    "book_sheet.sheet as book_sheet_size_label",
                    "title_sheet.sheet as title_sheet_size_label"
                )
                ->where('product_tbl.product_type', '=', 1)
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
            ->where('product_tbl.product_type', '=', 1)
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

        $products = DB::table("product_tbl")
        ->select('id', 'product_name as name' )
        ->where('product_type', '=', 1)
            ->orderBy("id", "DESC")
            ->get();

        return response()->json([
            "success" => true,
            "products" => $products
        ]);
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
            "farmay" => $request->farmay,
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
                ->leftJoin("subject_tbl", "subject_tbl.id", "product_tbl.subject")
                ->leftJoin("book_for_board_tbl", "book_for_board_tbl.id", "product_tbl.book_for")
                ->leftJoin("sheet_size_tbl as book_sheet", "book_sheet.id", "product_tbl.book_sheet_size")
                ->leftJoin("sheet_size_tbl as title_sheet", "title_sheet.id", "product_tbl.title_sheet_size")
                ->orderBy("id", "DESC")
                ->select(
                    "product_tbl.*",
                    "category_tbl.category as category_name",
                    "subject_tbl.subject as subject_name",
                    "book_for_board_tbl.name as board_name",
                    "book_sheet.sheet as book_sheet_size_label",
                    "title_sheet.sheet as title_sheet_size_label"
                )
                ->where('product_type', '=', 1)
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

    public function get_products_for_batch(Request $request){
        $result = DB::table('product_tbl')
        ->select('product_tbl.id', 'product_name as name', 'pages', 'inner_pages', 'rule_pages',
        'sheet_tbl.sheet', 'sheet_tbl.portion as s_portion', 'sheet_tbl.length as s_length',
        'sheet_tbl.width as s_width', 'title_tbl.sheet as title', 'title_tbl.portion as t_portion',
        'title_tbl.length as t_length', 'title_tbl.width as t_width', 'farmay')
        ->leftjoin('sheet_size_tbl as sheet_tbl', 'sheet_tbl.id', '=', 'product_tbl.book_sheet_size')
        ->leftjoin('sheet_size_tbl as title_tbl', 'title_tbl.id', '=', 'product_tbl.title_sheet_size')
        ->where('active', '=', 1)
        ->where('product_type', '=', 1)
        ->orderBy('id', 'DESC')
        ->get();

        return response()->json([
            "sucess" => 1,
            "products" => $result,
        ]);
    }

}
