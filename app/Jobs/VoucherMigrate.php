<?php

namespace App\Jobs;

use App\Jobs\Job;
use \App\Modules\Promotion\Models\Voucher; // voucer_code
use \App\Modules\Promotion\Models\VoucherTemplate;
use \App\Modules\Promotion\Models\VoucherCondition;
use \App\Modules\Promotion\Models\Promotion; // promotion_code
use \App\Modules\Promotion\Models\PromotionTemplate;
use \App\Modules\Promotion\Models\PromotionCondition;
use \App\Modules\Promotion\Models\ActiveJob;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class VoucherMigrate extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $last_id;
    protected $continue;
    protected $operator;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($last_id, $continue)
    {
        $this->last_id  = $last_id;
        $this->continue = $continue;
        $this->operator = '>';
        
        if($continue == false) {
            $this->operator = '=';
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $max_voucher_id = 0;
        \DB::enableQueryLog();

        $vtemplates = VoucherTemplate::where('voucher_template_id', $this->operator, $this->last_id)
                        ->orderBy('voucher_template_id', 'asc')
                        ->paginate(200);

        $querylog = \DB::getQueryLog();
        \Log::info($querylog);

        foreach($vtemplates as $vtemplate) {
            $template_mode_value    = $vtemplate->voucher_template_mode_value;
            $voucher_template_type  = $vtemplate->voucher_template_type;
            $exclusive_voucher      = $vtemplate->exklusive_voucher;
            $allow_benka_point      = 0;
            $trans_percustomer      = 0;

            $exclude_sale_item = 0;
            if($vtemplate->free_cheapest_item == 1) {
                $template_mode = 3;
                $template_mode_value = NULL;
            } else {
                switch($vtemplate->voucher_template_mode) {
                    case 1:
                        $template_mode = 2;
                        break;
                    case 2:
                        $template_mode = 1;
                        break;
                    case 7:
                        $template_mode      = ($vtemplate->voucher_template_mode_value > 100) ? 1 : 2;
                        $exclude_sale_item  = 1;
                        break;
                    default:
                        $template_mode       = 1;
                        $template_mode_value = 0;
                }
            }

            if($voucher_template_type == 1 || $voucher_template_type == 3) $trans_percustomer = 1;

            switch ($exclusive_voucher) {
                case 1:
                    $allow_benka_point = 0;
                    $exclusive_voucher = 1;
                    break;
                case 2:
                    $allow_benka_point = 0;
                    break;
                case 3:
                    $exclusive_voucher = 1;
            }


            $ptemplate = PromotionTemplate::create([
                    'promotions_template_name'              => $vtemplate->voucher_template_name,
                    'promotions_template_name_for_customer' => $vtemplate->voucher_template_name_for_customer,
                    'promotions_template_prefix'            => $vtemplate->voucher_template_prefix,
                    'promotions_template_length'            => $vtemplate->voucher_template_length,
                    'promotions_template_type_rule'         => $vtemplate->voucher_template_type_rule,
                    'promotions_template_mode'              => $template_mode,
                    'promotions_template_mode_value'        => $template_mode_value,
                    'promotions_template_one_multiple'      => $vtemplate->voucher_template_one_multiple,
                    'promotions_template_applicable'        => 1,
                    'start_date'                            => $vtemplate->start_date,
                    'end_date'                              => $vtemplate->end_date,
                    'enabled'                               => $vtemplate->enabled,
                    'created_by'                            => $vtemplate->created_by,
                    'createddate'                           => $vtemplate->createddate,
                    'updated_by'                            => $vtemplate->updated_by,
                    'updateddate'                           => $vtemplate->updateddate,
                    'domain_id'                             => $vtemplate->domain_id,
                    'free_shipping'                         => $vtemplate->free_shipping,
                    'free_cheapest_item'                    => $vtemplate->free_cheapest_item,
                    'max_discount_value'                    => $vtemplate->max_discount_value,
                    'eksklusif_voucher'                     => $exclusive_voucher,
                    'is_freegift_or_voucher'                => 2,
                    'exclude_sale_item'                     => $exclude_sale_item,
                    'allow_benka_point'                     => $allow_benka_point,
                    'one_transaction_per_customer'          => $trans_percustomer,
                    'voucher_template_id'                   => $vtemplate->voucher_template_id,
                ]);

            $max_voucher_id = $vtemplate->voucher_template_id;
            \Log::info('created row'. $max_voucher_id);
        }

        $active_job = ActiveJob::where('table_name', '=', 'voucher_template')->first();
        
        if(!empty($active_job)) {
            if($max_voucher_id == 0) {
                \Log::info('there is no exist voucher_template row');

                exit;
            }

            ActiveJob::where('table_name', '=', 'voucher_template')->update(['last_row' => $max_voucher_id]);

            \Log::info('new row inserted '. $max_voucher_id);

        } else {
            ActiveJob::create([
                    'table_name' => 'voucher_template',
                    'last_row'   => $max_voucher_id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
        }

        $promotions_con = VoucherCondition::where('voucher_template_id', $max_voucher_id)->get();

        foreach($promotions_con as $row) {
            app('App\Modules\Promotion\Controllers\PromotionController')->migrateVoucherCondition($row->voucher_condition_id);
        }

        $voucher_promo = PromotionTemplate::where('voucher_template_id', $max_voucher_id)->first();
        app('App\Modules\Promotion\Controllers\PromotionController')->updatePromo($voucher_promo->promotions_template_id);
//        if($this->continue == true) {
//            //Recall controller to trigger dispatcher
//            app('App\Modules\Promotion\Controllers\PromotionController')->migrateVoucherTemplate(0);
//        }

    }
}
