<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaperController extends Controller{

    public function add_new_paper(Request $request){
        $result = DB::table("product_tbl")->insert([

            "product_code" => $request-> paper,
            "product_sname" => $request-> paper,
            "product_name" => $request-> paper,
            "face_price" => 0,
            "pages" => 0,
            "inner_pages" => 0,
            "rule_pages" => 0,
            "amount_of_farmay" => 0,
            "weight" => $request->weight,
            "subject" => 0,
            "book_for" => 0,
            "book_sheet_size" => 0,
            "title_sheet_size" => 0,
            "category" => 0,
            "length" => $request -> length,
            "width" => $request -> width,
            "product_type" => 2,
            "child_type" => $request -> paper_type

        ]);
        if ($result == 1 || $result == 0){
            $papers = DB::table("product_tbl")
            ->join('product_child_type_tbl', 'product_tbl.child_type', '=', 'product_child_type_tbl.id')
            ->select('product_tbl.id', 'product_tbl.product_name', 'product_tbl.length', 'product_tbl.width', 'product_tbl.weight', 'product_child_type_tbl.child_type')
            ->where('product_tbl.product_type', '=', 2)    
            ->orderBy("id", "DESC")
                ->get();
            return response()->json([
                "success" => 1,
                "papers" => $papers
            ]);
        }else{
            return response()->json([
                "success" => 0
            ]);
        }
    }

    public function get_papers(Request $request){
        $papers = DB::table("product_tbl")
            ->join('product_child_type_tbl', 'product_tbl.child_type', '=', 'product_child_type_tbl.id')
            ->select('product_tbl.id', 'product_tbl.product_name', 'product_tbl.length', 'product_tbl.width', 'product_tbl.weight', 'product_child_type_tbl.child_type')
            ->where('product_tbl.product_type', '=', 2)     
            ->orderBy("id", "DESC")
                ->get();

        return response()->json([
            "success" => true,
            "papers" => $papers
        ]);
    }
}

