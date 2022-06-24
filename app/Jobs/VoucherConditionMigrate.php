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

class VoucherConditionMigrate extends Job implements ShouldQueue
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


        $vconditions= VoucherCondition::select(\DB::connection('read_mysql')->raw('promotions_template.promotions_template_id, voucher_condition.*'))
                                        ->join('promotions_template', 'promotions_template.voucher_template_id', '=', 'voucher_condition.voucher_template_id')
                                        ->where('voucher_condition_id', $this->operator, $this->last_id)
                                        ->orderBy('voucher_condition_id', 'asc')
                                        ->paginate(200);

        foreach($vconditions as $vcondition) {
            $equal_type             = $vcondition->voucher_type_equal_type;
            $voucher_type_condition = $vcondition->voucher_type_condition;

            if($voucher_type_condition == 15 || $voucher_type_condition == 27 || $voucher_type_condition == 14 || $voucher_type_condition == 13) {
                $equal_type = 9;
            }else if($voucher_type_condition == 22 || $voucher_type_condition == 20 || $voucher_type_condition == 7 || $voucher_type_condition == 22) {
                $equal_type = 1;
            }else if($voucher_type_condition == 4) {
                $equal_type = 3;
            }else if($voucher_type_condition == 5 || $voucher_type_condition == 21) {
                $equal_type = 4;
            }

            $pcondition = PromotionCondition::create([
                    'promotions_condition_parent_id' => $vcondition->voucher_condition_parent_id,
                    'promotions_template_id'         => $vcondition->promotions_template_id,
                    'promotions_type_condition'      => $vcondition->voucher_type_condition,
                    'promotions_type_all_required'   => $vcondition->voucher_type_all_required,
                    'promotions_type_rules_type'     => $vcondition->voucher_type_rules_type,
                    'promotions_type_equal_type'     => $equal_type,
                    'promotions_type_equal_value'    => $vcondition->voucher_type_equal_value,
                    'use_as_action_condition'        => $vcondition->use_as_action_condition,
                    'voucher_condition_id'           => $vcondition->voucher_condition_id,
                ]);

            $max_voucher_id = $vcondition->voucher_condition_id;
            \Log::info('id = '. $max_voucher_id);
        }

        $active_job = ActiveJob::where('table_name', '=', 'voucher_condition')->first();
        if(!empty($active_job)) {
            
            if($max_voucher_id == 0) {
                \Log::info('there is no exist voucher_condition row');
                exit;
            }

            $update_act_job = ActiveJob::where('table_name', '=', 'voucher_condition')->update(['last_row' => $max_voucher_id]);

        } else {
            ActiveJob::create([
                    'table_name'    => 'voucher_condition',
                    'last_row'      => $max_voucher_id,
                    'created_at'    => date('Y-m-d H:i:s')
                ]);
        }

        if($this->continue == true) {
            //Recall controller to trigger dispatcher
            app('App\Modules\Promotion\Controllers\PromotionController')->migrateVoucherCondition(0);
        }
    }
}
