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
use \App\Modules\Promotion\Models\Gift;
use \App\Modules\Promotion\Models\GiftCondition;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GiftMigrate extends Job implements ShouldQueue
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
        $gift_type = 0;
        $max_voucher_id = 0;

        $gifts = Gift::where('gift_id', $this->operator, $this->last_id)
                        ->orderBy('gift_id', 'asc')
                        ->paginate(10);

        foreach($gifts as $gift) {
            $mode_value         = $gift->gift_type_value;
            $exclude_sale_item  = 0;

            if($gift->gift_type == 4) {
                $gift_type = 3;
                $mode_value = NULL;
            } else {
                switch($gift->gift_type) {
                    case 1 :
                        $gift_type  = 1;
                        $mode_value = 0;
                        break;
                    case 2 :
                        $gift_type  = 1;
                        break;
                    case 3 :
                        $gift_type  = 2;
                        break;
                    case 4 :
                        $gift_type  = 3;
                        $mode_value = 0;
                        break;
                    case 5 :
                        $gift_type  = 1;
                        $mode_value = 0;
                        break;
                    case 6 :
                        $gift_type  = 4;
                        $mode_value = 0;
                        break;
                    case 7 :
                        $gift_type          = 1;
                        $exclude_sale_item  = 1;
                        break;
                    default :
                        $gift_type = 1;
                }    
            }

            $gift = PromotionTemplate::create([
                    'promotions_template_name'              => $gift->gift_name,
                    'promotions_template_name_for_customer' => $gift->gift_name_for_customer,
                    'promotions_template_prefix'            => null,
                    'promotions_template_length'            => null,
                    'promotions_template_type_rule'         => 1,
                    'promotions_template_mode'              => $gift_type,
                    'promotions_template_mode_value'        => $mode_value,
                    'promotions_template_one_multiple'      => null,
                    'promotions_template_applicable'        => 1,
                    'start_date'                            => $gift->start_date,
                    'end_date'                              => $gift->end_date,
                    'enabled'                               => $gift->enabled,
                    'created_by'                            => $gift->created_by,
                    'createddate'                           => $gift->createddate,
                    'updated_by'                            => $gift->updated_by,
                    'updateddate'                           => $gift->updateddate,
                    'domain_id'                             => $gift->domain_id,
                    'free_shipping'                         => null,
                    'free_cheapest_item'                    => null,
                    'max_discount_value'                    => $gift->max_discount_value,
                    'eksklusif_voucher'                     => $gift->eksklusif,
                    'is_freegift_or_voucher'                => 1,
                    'exclude_sale_item'                     => $exclude_sale_item,
                    'allow_benka_point'                     => 0,
                    'one_transaction_per_customer'          => 0,
                    'gift_id'                               => $gift->gift_id,
                ]);

            $max_voucher_id = $gift->gift_id;
        }

        $active_job = ActiveJob::where('table_name', '=', 'gift')->first();
        if(!empty($active_job)) {

            if($max_voucher_id == 0) {
                \Log::info('there is no exist gift row');
                exit;
            }

            ActiveJob::where('table_name', '=', 'gift')->update(['last_row' => $max_voucher_id]);

        } else {
            ActiveJob::create([
                    'table_name'    => 'gift',
                    'last_row'      => $max_voucher_id,
                    'created_at'    => date('Y-m-d H:i:s')
                ]);
        }

        $gift_con = GiftCondition::where('gift_id', $max_voucher_id)->get();

        foreach($gift_con as $row) {
            app('App\Modules\Promotion\Controllers\PromotionController')->giftConditionMigrate($row->gift_condition_id);
        }

        $gift_promo = PromotionTemplate::where('gift_id', $max_voucher_id)->first();
        app('App\Modules\Promotion\Controllers\PromotionController')->updatePromo($gift_promo->promotions_template_id);

//        if($this->continue == true) {
//            //Recall controller to trigger dispatcher
//            app('App\Modules\Promotion\Controllers\PromotionController')->giftMigrate(0);
//        }
    }
}
