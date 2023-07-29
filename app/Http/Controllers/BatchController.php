<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class BatchController extends Controller{

    public function get_processes(Request $request){
        $result = DB::table('batch_process_tbl')
        ->orderBy('id', 'DESC')
        ->get();

        return response()->json([
            'sucess'=> '1',
            'processes' => $result
        ]);
    }
    public function add_new_batch(Request $request){
        $BatchNo = $this -> generateBatchNo();

        DB::beginTransaction();
        try{

        
        $result = DB::table('batch_tbl')->insert([
            'batch_no' =>  $BatchNo,
            'for_product' => $request -> for_product,
            'book_print_qty' => $request -> book_print_qty,
            'book_paper_qty' => $request -> book_paper_qty,
            'book_paper_wastage' => $request -> wastage,
            'title_print_qty' => $request -> book_print_qty,
            'title_paper_qty' => $request -> title_paper_qty,
            'title_paper_wastage' => $request -> wastage,
            'inner_paper_qty' => $request -> inner_paper_qty,
            'inner_print_qty' => $request -> book_print_qty,
            'inner_paper_wastage' => $request -> wastage,
            'rule_paper_qty' => $request -> rule_paper_qty,
            'rule_print_qty' => $request -> book_print_qty,
            'rule_paper_wastage' => $request -> wastage,
            'paper_for_book' => $request -> paper_for_book,
            'paper_for_inner' => $request -> paper_for_inner,
            'paper_for_rule' => $request -> paper_for_rule,
            'paper_for_title' => $request -> paper_for_title,
            'status' => $request -> status,
            'created_by' => $request -> created_by,


        ]);
$processes = $request -> batch_processes;

       foreach($processes as $process){
        $product = '';
        $paperQty = '';
        if($process['id'] == '1')
        {
            $product = $request -> paper_for_book;
            $paperQty = $request -> book_paper_qty;
        }
        else  if($process['id'] == '2')
        {
            $product = $request -> paper_for_title;
            $paperQty = $request -> title_paper_qty;
        }
        else  if($process['id'] == '3')
        {
            $product = $request -> paper_for_inner;
            $paperQty = $request -> inner_paper_qty;
        }
        else  if($process['id'] == '4')
        {
            $product = $request -> paper_for_rule;
            $paperQty = $request -> rule_paper_qty;
        }
        else  if($process['id'] == '5')
        {
            $product = 0;
            $paperQty = 0;
        }
        else  if($process['id'] == '6')
        {
            $product = 0;
            $paperQty = 0;
        }
        else  if($process['id'] == '7')
        {
            $product = 0;
            $paperQty = 0;
        }
        else{
            $product = 0;
            $paperQty = 0;
        }
        
        $batch = DB::table('batch_history_tbl')->insert([
            'batch_no' => $BatchNo,
            'process' => $process['id'],
            'order_qty' => $request -> book_print_qty,
            'raw_product' => $product,
            'paper_qty' => $paperQty,
           
        ]);
       }

       DB::commit();
if($result == 1){
    return response()->json([
        'success' => 1
        
    ]);
}

}
catch(Exception $e){
    DB::rollback();
    throw $e;
    return response()->json([
        'success' => 0
        
    ]);
}
        
    }
   
    public function generateBatchNo(){
        $currentDate = Date::now();
        $currentYear = $currentDate->format('Y');
        $currentMonth = $currentDate->format('m');

$count = DB::table("batch_tbl")
->whereYear('created_date', '=', $currentYear)
->whereMonth('created_date', '=', $currentMonth)
->count();

$count = $count + 1;
$batchNo = "SP-" . $currentYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
return $batchNo;
    }

    public function get_batches_against_processes(Request $request, $id){
        
$result = DB::table("batch_tbl")
->join("batch_history_tbl as bht", function($join){
	$join->on("batch_tbl.batch_no", "=", "bht.batch_no");
})
->select("batch_tbl.batch_no")
->where("batch_tbl.status", "=", 'open')
->where("bht.process", "=", $id)
->whereNull("bht.voucher_no")
->get();

        return response()->json([
            'success' => 1,
            'batches'=> $result,
            
        ]);
    }
    public function get_batches_for_product_received(Request $request, $vid){
  
        /*
$result = DB::table("batch_tbl")
->join("batch_history_tbl as bht", function($join){
	$join->on("batch_tbl.batch_no", "=", "bht.batch_no");
})
->select("batch_tbl.batch_no")
->where("batch_tbl.status", "=", 'open')
->where("bht.process", "=", 7)
->where("bht.voucher_no", "!=", NULL)
->get();
*/

$result = DB::table("batch_tbl")
->join("batch_history_tbl as bht", function($join){
	$join->on("batch_tbl.batch_no", "=", "bht.batch_no");
})
->join('voucher_tbl', 'bht.voucher_no', '=', 'voucher_tbl.voucher_no')
->select("batch_tbl.batch_no")
->where("batch_tbl.status", "=", 'open')
->where("bht.process", "=", 7)
->where("bht.voucher_no", "!=", NULL)
->where("voucher_tbl.account_code", '=', $vid)
->get();

        return response()->json([
            'success' => 1,
            'batches'=> $result,
            
        ]);
    }

    public function get_batch_data_for_press(Request $request, $batch, $process){
        $result = DB::table('batch_tbl as bt')
    ->join('product_tbl as pt', 'bt.for_product', '=', 'pt.id')
    ->join('batch_history_tbl as bht', 'bt.batch_no', '=', 'bht.batch_no')
    ->join('product_tbl as pt1', 'bht.raw_product', '=', 'pt1.id')
    ->select(
        'pt.product_name as productName',
        'bt.for_product as productID',
        'pt1.product_name as paperProduct',
        'bht.raw_product as paperProductID',
        'bht.order_qty as order',
        'bht.paper_qty as paperQty'
    )
    ->where('bht.batch_no', '=', $batch)
    ->where('bht.process', '=', $process)
    ->get();
if($result){
    return response()->json([
        'success' => 1,
        'batchData' => $result
    ]);
}
else{
    return response()->json([
        'success' => 0,
        
    ]);
}
        

    }

    public function get_batch_data_for_lamination(Request $request, $batch, $process, $recfrom){
       
$result = DB::select("
SELECT 
    pt.product_name AS productName,
    bt.for_product AS productID,
    bht.order_qty AS 'order',
    (select v_t.name from batch_history_tbl bht1 left join voucher_tbl vt1 on bht1.voucher_no = 
    vt1.voucher_no left join vendor_tbl v_t on vt1.account_code = v_t.code
    where bht1.batch_no = ? and bht1.process = ?) as 'Received from',
    (select vt1.account_code from batch_history_tbl bht1 left join voucher_tbl vt1 
    on bht1.voucher_no = vt1.voucher_no 
    where bht1.batch_no = ? and bht1.process = ?) as 'Received from ID'
FROM batch_tbl AS bt
JOIN product_tbl AS pt ON bt.for_product = pt.id
JOIN batch_history_tbl AS bht ON bt.batch_no = bht.batch_no
WHERE bht.batch_no = ?
  AND bht.process = ?
", [$batch, $recfrom, $batch, $recfrom, $batch, $process]);




if($result){
    return response()->json([
        'success' => 1,
        'batchData' => $result
    ]);
}
else{
    return response()->json([
        'success' => 0,
        
    ]);
}
        

    }

    public function get_batch_data_for_binding(Request $request, $batch, $process){
       
        $result = DB::select("
        SELECT 
            pt.product_name AS productName,
            bt.for_product AS productID,
            bht.order_qty AS 'order',
            
            (select v_t.name from batch_history_tbl bht1 left join voucher_tbl vt1 on bht1.voucher_no = 
            vt1.voucher_no left join vendor_tbl v_t on vt1.account_code = v_t.code
            where bht1.batch_no = ? and bht1.process = 1) as 'Received from',
            (select vt1.account_code from batch_history_tbl bht1 left join voucher_tbl vt1 
            on bht1.voucher_no = vt1.voucher_no 
            where bht1.batch_no = ? and bht1.process = 1) as 'Received from ID'
        FROM batch_tbl AS bt
        JOIN product_tbl AS pt ON bt.for_product = pt.id
        JOIN batch_history_tbl AS bht ON bt.batch_no = bht.batch_no
        WHERE bht.batch_no = ?
          AND bht.process = ?
        ", [$batch, $batch, $batch, $process]);
        
        
        
        
        if($result){
            return response()->json([
                'success' => 1,
                'batchData' => $result
            ]);
        }
        else{
            return response()->json([
                'success' => 0,
                
            ]);
        }
                
        
            }
    
            public function get_batch_data_for_book_received(Request $request, $batch){
       
                $result = DB::select("
                SELECT 
                    pt.product_name AS productName,
                    bt.for_product AS productID,
                    bht.order_qty AS 'order',
                    
                    (select IFNULL(sum(qtyin), 0) from inventory_tbl where batch_no = ? and description = bt.for_product) as hasReceived,                   
                    (select rate from temp_inventory_tbl where batch_no = ? and process = 7) as rate
                FROM batch_tbl AS bt
                JOIN product_tbl AS pt ON bt.for_product = pt.id
                JOIN batch_history_tbl AS bht ON bt.batch_no = bht.batch_no
                WHERE bht.batch_no = ?
                  AND bht.process = 7
                ", [$batch, $batch, $batch]);
                
                
                
                
                if($result){
                    return response()->json([
                        'success' => 1,
                        'batchData' => $result
                    ]);
                }
                else{
                    return response()->json([
                        'success' => 0,
                        
                    ]);
                }
                        
                
                    }

                    public function get_batches_for_pv_against_vendor(Request $request, $process, $vid){
/*
                        select bht.batch_no from batch_history_tbl bht
LEFT join voucher_tbl vt on bht.voucher_no = vt.voucher_no
where bht.process = 1 and bht.isbilled = 0 and vt.account_code = '02-01-001'
*/
                    $result = DB::table('batch_history_tbl as bht')
                    ->leftjoin('voucher_tbl as vt', 'bht.voucher_no', '=', 'vt.voucher_no')
                    ->select('bht.batch_no')
                    ->where('bht.process', '=', $process)
                    ->where('bht.isbilled', '=', 0)
                    ->where('vt.account_code', '=', $vid)
                    ->get();

                    if($result){
                        return response()->json([
                            'success' => 1,
                            'batches' => $result
                        ]);
                    }
                    else{
                        return response()->json([
                            'success' => 0,
                            
                        ]);
                    }

                    }
                    public function get_batch_data_for_press_pv(Request $request, $batch, $process){
                        $result = DB::table('temp_inventory_tbl as tit')
                        ->leftjoin('batch_tbl as bt', 'tit.batch_no', '=', 'bt.batch_no')
                        ->leftjoin('product_tbl as pt', 'bt.for_product', '=', 'pt.id')
                        ->select('pt.product_name as productName', 'pt.id as productID', 'tit.plates', 'tit.qty', 'tit.rate', 'tit.amount')
                        ->where('tit.batch_no', '=', $batch)
                        ->where('tit.process', '=', $process)
                        ->get();

                        if($result){
                            return response()->json([
                                'success' => 1,
                                'batchData' => $result
                            ]);
                        }
                        else{
                            return response()->json([
                                'success' => 0,
                                
                            ]);
                        }
                    }
                    public function get_batch_data_for_binder_pv(Request $request, $batch, $process){
                        $result = DB::table('temp_inventory_tbl AS tit')
                        ->select(
                        'pt.product_name AS productName',
                        'pt.id AS productID',
                        'tit.qty AS printOrder',
                        'tit.rate',
                        'tit.amount',
                        DB::raw("(SELECT SUM(qtyin) FROM inventory_tbl WHERE voucher_no LIKE 'BR%' AND batch_no = '$batch') AS receivedQty")
                            )
                        ->leftJoin('batch_tbl AS bt', 'tit.batch_no', '=', 'bt.batch_no')
                        ->leftJoin('product_tbl AS pt', 'bt.for_product', '=', 'pt.id')
                        ->where('tit.batch_no', '=', $batch) // Replace 'SP-2023-07-0001' with the actual value of $batch
                        ->where('tit.process', '=', $process) // Replace '7' with the actual value of $process
                        ->get();

                        if($result){
                            return response()->json([
                                'success' => 1,
                                'batchData' => $result
                            ]);
                        }
                        else{
                            return response()->json([
                                'success' => 0,
                                
                            ]);
                        }
                    }
}