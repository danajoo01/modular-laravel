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

class VoucherCodeMigrate extends Job implements ShouldQueue
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
        \DB::enableQueryLog();
        $max_voucher_id = 0;

        /*$vcodes = Voucher::select(\DB::raw('promotions_template.promotions_template_id, voucher_code.*'))
                            ->join('promotions_template', 'promotions_template.voucher_template_id', '=', 'voucher_code.voucher_template_id')
                            // ->whereRaw('year(voucher_template.createddate) = 2016')
                            ->where('voucher_code_id', '>', $this->last_id)
                            // ->whereIn('promotions_template.voucher_template_id', [3,169])
                            // ->whereNotIn('promotions_template.voucher_template_id', [3,169])
                            // ->whereRaw('voucher_code.createddate > NOW() - INTERVAL 30 DAY')
                            ->orderBy('voucher_code_id', 'asc')
                            ->paginate(200);*/

        $vcodes = Voucher::where('voucher_code_id', $this->operator, $this->last_id)
//                            ->whereRaw("date(createddate) = curdate()")
                            ->orderBy('voucher_code_id', 'asc')
                            ->paginate(500);

        $querylog = \DB::getQueryLog();
        \Log::info($querylog);

        foreach($vcodes as $vcode) {
            $promo_template = PromotionTemplate::where('voucher_template_id', '=', $vcode->voucher_template_id)
                                                ->first();
            if(!empty($promo_template)) {
                \Log::info('promo_template_id -> '. $promo_template->promotions_template_id .'\n\n');
                $promo_code = Promotion::create([
                        'promotions_template_id'    => $promo_template->promotions_template_id,
                        'promotions_code_usage'     => $vcode->voucher_code_usage,
                        'promotions_code_number'    => $vcode->voucher_code_number,
                        'promotions_code_usage'     => $vcode->voucher_code_usage,
                        'customer_email'            => $vcode->customer_email,
                        'status'                    => $vcode->status,
                        'duration'                  => $vcode->duration,
                        'created_by'                => $vcode->created_by,
                        'createddate'               => $vcode->createddate
                    ]);               
            }

            $max_voucher_id = $vcode->voucher_code_id;
        }

        $active_job = ActiveJob::where('table_name', '=', 'voucher_code')->first();
        if(!empty($active_job)) {
            
            if($max_voucher_id == 0) {
                \Log::info('there is no exist voucher_code row');
                exit;
            }

            ActiveJob::where('table_name', '=', 'voucher_code')->update(['last_row' => $max_voucher_id]);

        } else {
            ActiveJob::create([
                    'table_name' => 'voucher_code',
                    'last_row' => $max_voucher_id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
        }

        if($this->continue == true) {
            //Recall controller to trigger dispatcher
            app('App\Modules\Promotion\Controllers\PromotionController')->migrateVoucherCode(0);
        }
    }
}
