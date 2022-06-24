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

class PromotionConditionNewRow extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

   protected $last_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($last_id)
    {
        $this->last_id = $last_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('last id = '. $this->last_id);

        \DB::enableQueryLog();
        $max_voucher_id = 0;

        $vtemplates = PromotionTemplate::select(\DB::connection('read_mysql')->raw(
                                        'promotions_condition.promotions_condition_id,
                                        promotions_template.promotions_template_id,
                                        promotions_template.domain_id'))
                                    ->join('promotions_condition', 'promotions_template.promotions_template_id', '=', 'promotions_condition.promotions_template_id')
                                    ->where('promotions_template.promotions_template_id', '>', $this->last_id)
                                    ->where('promotions_condition.promotions_condition_parent_id', '=', 0)
                                    ->whereIn('domain_id', ['1','2','3'])
                                    ->paginate(100);
        
        $querylog = \DB::getQueryLog();
        \Log::info($querylog);

        \Log::info('row'. $max_voucher_id);


        foreach($vtemplates as $vtemplate) {
            \Log::info('row'. $vtemplate->promotions_template_id);

            $pcondition = PromotionCondition::create([
                    'promotions_condition_parent_id' => $vtemplate->promotions_condition_id,
                    'promotions_template_id' => $vtemplate->promotions_template_id,
                    'promotions_type_condition' => 30,
                    'promotions_type_all_required' => 0,
                    'promotions_type_rules_type' => 5,
                    'promotions_type_equal_type' => 1,
                    'promotions_type_equal_value' => $vtemplate->domain_id,
                    'use_as_action_condition' => 0,
                ]);


            $max_voucher_id = $vtemplate->promotions_template_id;
            \Log::info('max row'. $max_voucher_id);
        }

        $active_job = ActiveJob::where('table_name', '=', 'promotions_condition')->first();
        if(!empty($active_job)) {

            if($max_voucher_id == 0) {
                \Log::info('there is no exist Promotion new row');
                exit;
            }

            ActiveJob::where('table_name', '=', 'promotions_condition')->update(['last_row' => $max_voucher_id]);

        } else {
            ActiveJob::create([
                    'table_name' => 'promotions_condition',
                    'last_row' => $max_voucher_id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
        }

        //Recall controller to trigger dispatcher
        app('App\Modules\Promotion\Controllers\PromotionController')->PromotionConditionNewRow();
    }
}
