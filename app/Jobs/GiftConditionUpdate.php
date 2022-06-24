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

class GiftConditionUpdate extends Job implements ShouldQueue
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



        $promotions = PromotionCondition::where('promotions_condition_id', $this->operator, $this->last_id)
                                        ->where('promotions_condition_parent_id', '<>', 0)
//                                        ->whereRaw('date(promotions_template.createddate) = curdate()')
//                                        ->whereRaw("date(promotions_template.createddate) < '2016-08-03'")
                                        ->whereRaw('gift_condition_id is not null')
                                        ->paginate(200);

        foreach($promotions as $promotion) {
            \DB::enableQueryLog();

            $pselect = PromotionCondition::where('gift_condition_id', $promotion->promotions_condition_parent_id)
                                        ->first();

            $querylog = \DB::getQueryLog();
            \Log::info($querylog);

            if(!empty($pselect)) {
                $pupdate = PromotionCondition::where('promotions_condition_parent_id', $promotion->promotions_condition_parent_id)
                                            ->update(['promotions_condition_parent_id' => $pselect->promotions_condition_id]);
            }

            $max_voucher_id = $promotion->promotions_condition_id;
        }

        $active_job = ActiveJob::where('table_name', '=', 'gift_condition_update')->first();
        if(!empty($active_job)) {
            
            if($max_voucher_id == 0) {
                \Log::info('there is no exist gift condition update row');
                exit;
            }

            ActiveJob::where('table_name', '=', 'gift_condition_update')
                        ->update(['last_row' => $max_voucher_id]);
        } else {
            ActiveJob::create([
                    'table_name' => 'gift_condition_update',
                    'last_row' => $max_voucher_id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
        }

        if($this->continue == true) {
            //Recall controller to trigger dispatcher
            app('App\Modules\Promotion\Controllers\PromotionController')->giftConditionUpdate(0);
        }
    }
}
