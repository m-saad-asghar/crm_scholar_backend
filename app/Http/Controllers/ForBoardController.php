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
                "boards" => $boards
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
    public function update_book_for_board(Request $request, $id){
        $update = DB::table(('book_for_board_tbl'))
        ->where('id', '=', $id)
        ->update([
            "name" => $request -> board, 
        ]);

        if($update === 1){
            $boards = DB::table("book_for_board_tbl")
            ->orderBy("id", "DESC")
            ->get();
    
            return response()->json([
                "success" => 1,
                "boards" => $boards
            ]);
        }
        else{
            return response()->json([
                "success" => 0,
                
            ]);
        }
        
        
    }
    public function change_status_book_for_board(Request $request, $id){
        $result = DB::table("book_for_board_tbl")->where("id", $id)->update([
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


