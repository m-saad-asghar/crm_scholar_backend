<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Exception;

class AccountVoucherController extends Controller{

    public function generateVoucher(){
        $currentDate = Date::now();
        $currentYear = $currentDate->format('Y');
        $currentMonth = $currentDate->format('m');

$count = DB::table("voucher_tbl")
->where('voucher_no', 'like', 'CP-%')
->whereYear('created_date', '=', $currentYear)
->whereMonth('created_date', '=', $currentMonth)
->count();

$count = $count + 1;
$voucher = "CP-" . $currentYear . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . str_pad($count, 4, '0', STR_PAD_LEFT);

return $voucher;
    }

    public function add_new_account_voucher(Request $request){
        $voucher = $this -> generateVoucher();
        $currentDate = Date::now();

        DB::beginTransaction();
//try{

    $result = DB::table("voucher_tbl")->insert([
        "voucher_no" => $voucher,
        "account_code" => $request -> Voucher['account_code'],
        "voucher_mode" =>  0,  // Payment
        "gross_amount" => $request -> Voucher['cash_amount'],
        "discount" => 0,
        "net_amount" => $request -> Voucher['cash_amount'],
        "ref_no" => '',
        "created_date" => $currentDate,
        "created_by" => 1,
        "voucher_type" => 'Cash Payment',
        
    ]);
    

    $register = DB::table('account_register_tbl')->insert([
        "account_code" => $request -> Voucher['account_code'],
        "voucher_no" => $voucher,
        "dr" => 0,
        "cr" => $request -> Voucher['cash_amount'],
        "remarks" => 'Cash Payment Voucher',

    ]);

    
    
    $inventories = $request -> inventories;

        foreach($inventories as $inventory){
            $register = DB::table('account_register_tbl')->insert([
                "account_code" => $inventory['account_code'],
                "voucher_no" => $voucher,
                "dr" => $inventory['dr'],
                "cr" => $inventory['cr'],
                "remarks" => $inventory['remarks'],
        
            ]);

        }

    DB::commit();

    return response()->json([
        "success" => 1,
        "voucher" => $voucher,
        
    ]);
/*}
catch(Exception $e){
DB::rollback();

return response()->json([
    "success" => 0,
    "voucher" => $voucher,
    
]);
} */
    }

    public function get_accounts_by_type(Request $request, $atype){
        $result = DB::table('chart_of_account_tbl as coa')
        ->select('coa.code', 'coa.account_name')
        ->where('coa.code', 'like', $atype)
        ->where('coa.is_parent', '=', 0)
        ->get();

        return response() -> json([
            'accounts' => $result,
        ]);
    }
}