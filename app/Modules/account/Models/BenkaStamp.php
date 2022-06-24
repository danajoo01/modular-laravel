<?php 

namespace App\Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Auth;

class BenkaStamp extends Model {

    public static function beginTransaction(){
        DB::beginTransaction();
    }

    public static function commitTransaction(){
        DB::commit();
    }

    public static function rollbackTransaction(){
        DB::rollBack();
    }

    public static function StampHistory($customer_id = null, $paginate = 10){
        $result = [];
        
        if($customer_id != null){
            $result = DB::table('benka_stamp_history')
                    ->select('id', 'history_create_date', 'stamp_value', 'type', 'description', 'stamp_status', 'purchase_code')                    
                    ->where('customer_id', '=', $customer_id)
                    ->where('stamp_value', '!=', 0)
                    ->orderBy('id', 'desc')
                    ->paginate($paginate);            
        }
        
        return $result;
    }

    public static function StampDeals($paginate = 6){
        $result = DB::table('benka_stamp_deals')
                ->select('id', 'deals_name', 'deals_description', 'stamp_price','deals_image')
                ->where('status', '=', 1)
                ->where('is_deleted', '=', 0)
                ->orderBy('id', 'desc')
                ->paginate($paginate);            
    
        return $result;
    }

    public static function StampDealsDetail($id = null){
        $result = [];

        if($id != null){
            $result = DB::table('benka_stamp_deals')
                    ->select('id', 'deals_name', 'deals_description', 'stamp_price','deals_image')
                    ->where('status', '=', 1)
                    ->where('is_deleted', '=', 0)
                    ->where('id', '=', $id)
                    ->first();            
        }

        return $result;
    }

    public static function GetStampActive($id = null){
        $result = [];

        if($id != null){
            $result = DB::table('customer')
                    ->select('customer_id', 'customer_email', 'customer_fname', 'customer_lname', 'stamp_active', 'stamp_expiry_date')
                    ->where('customer_id', '=', $id)
                    ->first();            
        }

        return $result;
    }

    public static function InsertLogHistory($data){
        
        if(!empty($data)){
           DB::table('benka_stamp_history')->insert($data);

           return true;       
        }

        return false;
    }

    public static function UpdateCustomer($id = null, $data = null){
        if(empty(Auth::user())){
            return false;
        }

        if($id != null){
            $update = DB::table('customer')
                ->where('customer_id', $id)
                ->update($data);

            return $update;
        }

        return false;
    }

    public static function InsertRedeem($data){
        
        if(!empty($data)){
           DB::table('benka_stamp_redeem')->insert($data);

           return true;       
        }

        return false;
    }
}
