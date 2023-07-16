<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller{

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

    public function get_subjects(Request $request){
        $subject = DB::table("subject_tbl")
        ->orderBy("id", "DESC")
        ->get();

        return response()->json([
            "success" => true,
            "subject" => $subject
        ]);
    }
}


