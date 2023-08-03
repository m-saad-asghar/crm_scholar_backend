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
    public function update_subject(Request $request, $id){
        $update = DB::table(('subject_tbl'))
        ->where('id', '=', $id)
        ->update([
            "subject" => $request -> subject, 
        ]);

        if($update === 1){
            $subject = DB::table("subject_tbl")
            ->orderBy("id", "DESC")
            ->get();
    
            return response()->json([
                "success" => 1,
                "subjects" => $subject
            ]);
        }
        else{
            return response()->json([
                "success" => 0,
                
            ]);
        }
        
        
    }
    public function change_status_subject(Request $request, $id){
        $result = DB::table("subject_tbl")->where("id", $id)->update([
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


