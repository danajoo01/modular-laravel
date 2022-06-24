<?php

namespace App\Jobs;

use App\Jobs\Job;
use \App\Modules\Promotion\Models\ActiveJob;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdatePromoParent extends Job implements ShouldQueue
{
    protected $last_id;
    protected $continue;
    protected $operator;
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($last_id, $continue)
    {
        $this->date     = date('Y-m-d H:i:s');
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
        $max_voucher_id = 0;
        \DB::enableQueryLog();

        $query = "SELECT 
                      A.promotions_template_id, A.promotions_condition_id
                    FROM
                      (SELECT 
                        promotions_condition_id,
                        promotions_template_id 
                      FROM
                        promotions_condition 
                      WHERE promotions_condition_parent_id = 0) A,
                      (SELECT 
                        promotions_condition_parent_id,
                        promotions_template_id 
                      FROM
                        promotions_condition 
                      WHERE promotions_condition_parent_id != 0) B 
                    WHERE A.promotions_condition_id != B.promotions_condition_parent_id 
                      AND A.promotions_template_id = B.promotions_template_id  
                      AND A.promotions_template_id NOT IN
                        (
                            SELECT promotions_template_id
                            FROM promotions_condition 
                            WHERE promotions_condition_parent_id = 0
                            GROUP BY promotions_template_id
                            HAVING (COUNT(promotions_template_id)) > 1
                            ORDER BY promotions_template_id DESC
                        )
                      AND A.promotions_template_id ". $this->operator ." ". $this->last_id ."
                    GROUP BY A.promotions_template_id 
                    ORDER BY A.promotions_template_id ASC
                    LIMIT 10";

        $parent_conditions = \DB::select(\DB::connection('read_mysql')->raw($query));

        $querylog = \DB::getQueryLog();
        \Log::info($querylog);

        foreach ($parent_conditions as $condition) {
            $promotions_template_id   = $condition->promotions_template_id;
            $promotions_condition_id  = $condition->promotions_condition_id;

            $update_item = array();
            $update_item['promotions_condition_parent_id'] = $promotions_condition_id;
            $update_inventory = \DB::table('promotions_condition')
                ->where('promotions_template_id', $promotions_template_id)
                ->where('promotions_condition_parent_id', '<>' , 0)
                ->update($update_item);

            $max_voucher_id = $promotions_template_id;
        }

        $active_job = ActiveJob::where('table_name', '=', 'update_promo')->first();
        if(!empty($active_job)) {

            if($max_voucher_id == 0) {
                \Log::info('there is no exist update_promo row');
                exit;
            }

            ActiveJob::where('table_name', '=', 'update_promo')->update(['last_row' => $max_voucher_id]);

        } else {
            ActiveJob::create([
                'table_name'    => 'update_promo',
                'last_row'      => $max_voucher_id,
                'created_at'    => $this->date
            ]);
        }

        if($this->continue == true) {
            //Recall controller to trigger dispatcher
            app('App\Modules\Promotion\Controllers\PromotionController')->updatePromo(0);
        }
    }
}
