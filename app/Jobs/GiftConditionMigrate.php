<?php

namespace App\Jobs;

use App\Jobs\Job;
use \App\Modules\Promotion\Models\Voucher; // voucer_code
use \App\Modules\Promotion\Models\VoucherTemplate;
use \App\Modules\Promotion\Models\VoucherCondition;
use \App\Modules\Promotion\Models\Promotion; // promotion_code
use \App\Modules\Promotion\Models\PromotionTemplate;
use \App\Modules\Promotion\Models\PromotionCondition;
use \App\Modules\Promotion\Models\GiftCondition;
use \App\Modules\Promotion\Models\ActiveJob;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GiftConditionMigrate extends Job implements ShouldQueue
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
        $this->last_id = $last_id;
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

        $gconditions= GiftCondition::select(\DB::connection('read_mysql')->raw('promotions_template.promotions_template_id, gift_condition.*'))
                                    ->join('promotions_template', 'promotions_template.gift_id', '=', 'gift_condition.gift_id')
                                    ->where('gift_condition_id', $this->operator, $this->last_id)
//                                    ->whereRaw('date(promotions_template.createddate) = curdate()')
//                                    ->whereRaw("date(promotions_template.createddate) < '2016-08-03'")
                                    ->orderBy('gift_condition_id', 'asc')
                                    ->paginate(100);

        foreach($gconditions as $gcondition) {
            $equal_type = $gcondition->gift_type_equal_type;

            if($gcondition->gift_type_condition == 21) $equal_type = 4;

            if($gcondition->promotions_type_condition == 4 || $gcondition->promotions_type_condition == 5) $equal_type = 3;

            $pcondition = PromotionCondition::create([
                    'promotions_condition_parent_id' => $gcondition->gift_condition_parent_id,
                    'promotions_template_id'         => $gcondition->promotions_template_id,
                    'promotions_type_condition'      => $gcondition->gift_type_condition,
                    'promotions_type_all_required'   => $gcondition->gift_type_all_required,
                    'promotions_type_rules_type'     => $gcondition->gift_type_rules_type,
                    'promotions_type_equal_type'     => $equal_type,
                    'promotions_type_equal_value'    => $gcondition->gift_type_equal_value,
                    'use_as_action_condition'        => $gcondition->use_as_action_condition,
                    'gift_condition_id'              => $gcondition->gift_condition_id,
                ]);
            
            $max_voucher_id = $gcondition->gift_condition_id;
            \Log::info('id = '. $max_voucher_id);
        }

        $active_job = ActiveJob::where('table_name', '=', 'gift_condition')->first();
        if(!empty($active_job)) {
            
            if($max_voucher_id == 0) {
                \Log::info('there is no exist gift_condition row');
                exit;
            }

            $update_act_job = ActiveJob::where('table_name', '=', 'gift_condition')->update(['last_row' => $max_voucher_id]);

        } else {
            ActiveJob::create([
                    'table_name' => 'gift_condition',
                    'last_row' => $max_voucher_id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
        }

        if($this->continue == true) {
            //Recall controller to trigger dispatcher
            app('App\Modules\Promotion\Controllers\PromotionController')->giftConditionMigrate(0);
        }

    }
}
