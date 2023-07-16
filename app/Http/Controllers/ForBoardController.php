<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForBoardController extends Controller{

    public function add_new_book_for_board(Request $request){
        $result = DB::table("book_for_board_tbl")->insert([
            "name" => $request -> board,
        ]);
        if ($result == 1 || $result == 0){
            $boards = DB::table("book_for_board_tbl")
                ->orderBy("id", "DESC")
                ->get();
            return response()->json([
                "success" => 1,
                "subjects" => $boards
            ]);
        }else{
            return response()->json([
                "success" => 0
            ]);
        }
    }

    public function get_book_for_board(Request $request){
        $boards = DB::table("book_for_board_tbl")
        ->orderBy("id", "DESC")
        ->get();

        return response()->json([
            "success" => true,
            "boards" => $boards
        ]);

    }
}


