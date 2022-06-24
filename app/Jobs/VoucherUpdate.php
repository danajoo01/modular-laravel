<?php

namespace App\Jobs;

use \App\Modules\Promotion\Models\Voucher; // voucer_code
use \App\Modules\Promotion\Models\VoucherTemplate;
use \App\Modules\Promotion\Models\VoucherCondition;
use \App\Modules\Promotion\Models\Promotion; // promotion_code
use \App\Modules\Promotion\Models\PromotionTemplate;
use \App\Modules\Promotion\Models\PromotionCondition;
use \App\Modules\Promotion\Models\ActiveJob;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class VoucherUpdate extends Job implements ShouldQueue
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
        $get_template = VoucherTemplate::where('voucher_template_id', $this->id)->first();

        if(!empty($get_template)) {
            $template_mode_value = $get_template->voucher_template_mode_value;

            if ($get_template->free_cheapest_item == 1) {
                $template_mode = 3;
                $template_mode_value = NULL;
            } else {

                if ($get_template->voucher_template_mode == 1) {
                    $template_mode = 2;
                } elseif ($get_template->voucher_template_mode == 2) {
                    $template_mode = 1;
                } else {
                    $template_mode = 2;
                }
            }

            $data_update = [
                'promotions_template_name' => $get_template->voucher_template_name,
                'promotions_template_name_for_customer' => $get_template->voucher_template_name_for_customer,
                'promotions_template_prefix' => $get_template->voucher_template_prefix,
                'promotions_template_length' => $get_template->voucher_template_length,
                'promotions_template_type_rule' => $get_template->voucher_template_type_rule,
                'promotions_template_mode' => $template_mode,
                'promotions_template_mode_value' => $template_mode_value,
                'promotions_template_one_multiple' => $get_template->voucher_template_one_multiple,
                'promotions_template_applicable' => 1,
                'start_date' => $get_template->start_date,
                'end_date' => $get_template->end_date,
                'enabled' => $get_template->enabled,
                'created_by' => $get_template->created_by,
                'createddate' => $get_template->createddate,
                'updated_by' => $get_template->updated_by,
                'updateddate' => $get_template->updateddate,
                'domain_id' => $get_template->domain_id,
                'free_shipping' => $get_template->free_shipping,
                'free_cheapest_item' => $get_template->free_cheapest_item,
                'max_discount_value' => $get_template->max_discount_value,
                'eksklusif_voucher' => $get_template->eksklusif_voucher,
                'is_freegift_or_voucher' => 2,
                'exclude_sale_item' => 0,
                'allow_benka_point' => 0,
                'one_transaction_per_customer' => 0,
                'voucher_template_id' => $get_template->voucher_template_id,
            ];

            $update_promo = PromotionTemplate::where('voucher_template_id', $this->id)
                ->update($data_update);
        }
    }
}
