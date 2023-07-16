<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GodownController extends Controller{

    public function add_new_godown(Request $request){
        $result = DB::table("godown_tbl")->insert([
            "name" => $request -> name,
            "address" => $request -> address,
            "contact_no" => $request -> contact_no,
        ]);
        if ($result == 1 || $result == 0){
            $godowns = DB::table("godown_tbl")
                ->orderBy("id", "DESC")
                ->get();
            return response()->json([
                "success" => 1,
                "godowns" => $godowns
            ]);
        }else{
            return response()->json([
                "success" => 0
            ]);
        }
    }

    public function get_godowns(Request $request){
        $godowns = DB::table("godown_tbl")
        ->orderBy("id", "DESC")
        ->get();

        return response()->json([
            "success" => true,
            "godowns" => $godowns
        ]);
    }
}


