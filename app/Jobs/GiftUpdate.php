<?php

namespace App\Jobs;

use \App\Modules\Promotion\Models\Gift; // gift
use \App\Modules\Promotion\Models\PromotionTemplate;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GiftUpdate extends Job implements ShouldQueue
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
        $get_gift = Gift::where('gift_id', $this->id)->first();

        if(!empty($get_gift)) {
            $mode_value = $get_gift->gift_type_value;

            if ($get_gift->gift_type == 4) {
                $gift_type = 3;
                $mode_value = NULL;
            } else {
                switch ($get_gift->gift_type) {
                    case 2 :
                        $gift_type = 1;
                        break;
                    case 3 :
                        $gift_type = 2;
                        break;
                    case 4 :
                        $gift_type = 3;
                        break;
                    case 6 :
                        $gift_type = 4;
                        break;
                    default :
                        $gift_type = 1;
                }
            }

            $gift_data = [
                'promotions_template_name' => $get_gift->gift_name,
                'promotions_template_name_for_customer' => $get_gift->gift_name_for_customer,
                'promotions_template_prefix' => null,
                'promotions_template_length' => null,
                'promotions_template_type_rule' => 1,
                'promotions_template_mode' => $gift_type,
                'promotions_template_mode_value' => $get_gift->gift_type_value,
                'promotions_template_one_multiple' => null,
                'promotions_template_applicable' => 1,
                'start_date' => $get_gift->start_date,
                'end_date' => $get_gift->end_date,
                'enabled' => $get_gift->enabled,
                'created_by' => $get_gift->created_by,
                'createddate' => $get_gift->createddate,
                'updated_by' => $get_gift->updated_by,
                'updateddate' => $get_gift->updateddate,
                'domain_id' => $get_gift->domain_id,
                'free_shipping' => null,
                'free_cheapest_item' => null,
                'max_discount_value' => $get_gift->max_discount_value,
                'eksklusif_voucher' => $get_gift->eksklusif,
                'is_freegift_or_voucher' => 1,
                'exclude_sale_item' => 0,
                'allow_benka_point' => 0,
                'one_transaction_per_customer' => 0,
                'gift_id' => $get_gift->gift_id,
            ];

            $update_gift = PromotionTemplate::where('gift_id', $this->id)
                ->update($gift_data);
        }
    }
}
