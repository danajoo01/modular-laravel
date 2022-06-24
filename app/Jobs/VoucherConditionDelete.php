<?php

namespace App\Jobs;

use App\Jobs\Job;

use \App\Modules\Promotion\Models\PromotionTemplate;
use \App\Modules\Promotion\Models\PromotionCondition;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class VoucherConditionDelete extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $promo_template  = PromotionTemplate::where('voucher_template_id', $this->id)->first();

        PromotionCondition::where('promotions_template_id', $promo_template->promotions_template_id)->delete();
    }
}
