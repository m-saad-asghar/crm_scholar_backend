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
        /*$result = DB::table('batch_tbl')
        ->join('batch_history_tbl as bht', 'batch_tbl.batch_no', '=', 'bht.batch_no')
        ->select('batch_tbl.batch_no')
        ->where('batch_tbl.status', '=', 'open')
        ->where('bht.voucher_no', 'IS', NULL)
        ->where('bht.process', '=', $id)
        ->orderBy('batch_tbl.id')
        ->get();

        $query = $result->toSql();
*/
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
       /* $result = DB::table('batch_tbl as bt')
    ->join('product_tbl as pt', 'bt.for_product', '=', 'pt.id')
    ->join('batch_history_tbl as bht', 'bt.batch_no', '=', 'bht.batch_no')
    ->select(
        'pt.product_name as productName',
        'bt.for_product as productID',        
        'bht.order_qty as order',        
    )
    ->where('bht.batch_no', '=', $batch)
    ->where('bht.process', '=', $process)
    ->get();
    

   $result = DB::table("batch_tbl as bt")
->join("product_tbl as pt", function($join){
	$join->on("bt.for_product", "=", "pt.id");
})
->join("batch_history_tbl as bht", function($join){
	$join->on("bt.batch_no", "=", "bht.batch_no");
})
->select("pt.product_name as productname", "bt.for_product as productid", 
"bht.order_qty as 'order'", "(select v_t.name from batch_history_tbl bht1 
left join voucher_tbl vt1 on bht1.voucher_no = vt1.voucher_no 
left join vendor_tbl v_t on vt1.account_code = v_t.code 
where bht1.batch_no =", $batch, "and bht1.process = 1) as 'received from'")
->where("bht.batch_no", "=", $batch)
->where("bht.process", "=", $process)
->get();
*/
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
    
}