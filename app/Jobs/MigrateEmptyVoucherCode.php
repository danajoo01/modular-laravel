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

class MigrateEmptyVoucherCode extends Job implements ShouldQueue
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


        $max_voucher_id = 0;

        $vcodes = Voucher::select(\DB::connection('read_mysql')->raw('voucher_code.*'))
                            ->leftJoin('promotions_code', 'voucher_code.voucher_code_number', '=', 'promotions_code.promotions_code_number')
                            ->whereRaw('promotions_code.promotions_code_number IS NULL')
                            ->whereRaw('voucher_code.`voucher_code_number` IS NOT NULL')
                            ->whereRaw('voucher_code.`createddate` IS NOT NULL')
                            ->where('voucher_code.voucher_code_id', '>', $this->last_id)
                            ->orderBy('voucher_code.voucher_code_id', 'asc')
                            ->paginate(10);

        foreach($vcodes as $vcode) {
            $promo_template = PromotionTemplate::where('voucher_template_id', '=', $vcode->voucher_template_id)
                                                ->first();

            \Log::info('this id'. $vcode->voucher_code_id);

            if(!empty($promo_template)) {
                \Log::info('promo_template_id -> '. $promo_template->promotions_template_id .'\n\n');

                \DB::enableQueryLog();

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

                $querylog = \DB::getQueryLog();
                \Log::info($querylog);

                \Log::info('new promo code-> '. $promo_code->promotions_code_id .'\n\n');
            }

            $max_voucher_id = $vcode->voucher_code_id;
        }

        $active_job = ActiveJob::where('table_name', '=', 'voucher_code_empty')->first();
        if(!empty($active_job)) {

            if($max_voucher_id == 0) {
                \Log::info('there is no exist voucher_code_empty row');
                exit;
            }

            ActiveJob::where('table_name', '=', 'voucher_code_empty')->update(['last_row' => $max_voucher_id]);
        } else {
            ActiveJob::create([
                'table_name' => 'voucher_code_empty',
                'last_row' => $max_voucher_id,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        //Recall controller to trigger dispatcher
        app('App\Modules\Promotion\Controllers\PromotionController')->emptyVoucherCode();
    }
}
