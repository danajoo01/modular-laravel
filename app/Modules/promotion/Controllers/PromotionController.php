<?php namespace App\Modules\Promotion\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;

use App\Jobs\MigrateEmptyVoucherCode;
use App\Jobs\UpdatePromoParent;
use App\Jobs\VoucherTemplateFix;
use \App\Modules\Promotion\Models\Promotion;
use \App\Modules\Promotion\Models\PromotionTemplate;
use \App\Modules\Promotion\Models\PromotionCondition;
use \App\Modules\Promotion\Models\Voucher;
use \App\Modules\Promotion\Models\ActiveJob;

use App\Jobs\GiftMigrate;
use App\Jobs\GiftConditionMigrate;
use App\Jobs\GiftConditionUpdate;
use App\Jobs\VoucherMigrate;
use App\Jobs\VoucherCodeMigrate;
use App\Jobs\VoucherConditionMigrate;
use App\Jobs\PromotionConditionNewRow;
use App\Jobs\UpdateParentPromotions;
use App\Jobs\VoucherUpdate;
use App\Jobs\GiftUpdate;
use App\Jobs\VoucherConditionDelete;
use App\Jobs\GiftConditionDelete;

use App\Modules\Promotion\Models\VoucherTemplate;
use Input;
use Validatoor;

use Illuminate\Http\Request;

class PromotionController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view("promotion::promotion.index");
    }

    /**
     * Migrate function.
     *
     * @return Response
     */
    public function migrateVoucherTemplate($id)
    {
        $continue = false;

        if($id == 0) {
            $active_job = ActiveJob::where('table_name', '=', 'voucher_template')->first();
            $id = !empty($active_job) ? $active_job->last_row : 0;
            $continue = true;
        }

        $job = (new VoucherMigrate($id, $continue));
        $this->dispatch($job);
    }

    public function migrateVoucherCode($id)
    {
        \Log::info('migrate voucher code --BEGIN--');

        $continue = false;

        if($id == 0) {
            $active_job = ActiveJob::where('table_name', '=', 'voucher_code')->first();
            $id = !empty($active_job) ? $active_job->last_row : 0;
            $continue = true;
        }

        $job = (new VoucherCodeMigrate($id, $continue));
        $this->dispatch($job);

        \Log::info('Begin migrate voucher code --FINISH--');
    }

    public function migrateVoucherCondition($id)
    {
        \Log::info('migrate voucher condition --BEGIN--');

        $continue = false;

        if($id == 0) {
            $active_job = ActiveJob::where('table_name', '=', 'voucher_condition')->first();
            $id = !empty($active_job) ? $active_job->last_row : 0;
            $continue = true;
        }

        $job = (new VoucherConditionMigrate($id, $continue));
        $this->dispatch($job);

        \Log::info('Begin migrate voucher code --FINISH--');
    }

    public function updatePromotionConditionParent($id)
    {
        \Log::info('migrate update voucher condition update --BEGIN--');

        $continue = false;

        if($id == 0) {
            $active_job = ActiveJob::where('table_name', '=', 'promotions_condition_update')->first();
            $id = !empty($active_job) ? $active_job->last_row : 0;
            $continue = true;
        }

        $job = (new UpdateParentPromotions($id, $continue));
        $this->dispatch($job);

        \Log::info('migrate update voucher code --FINISH--');
    }

    public function PromotionConditionNewRow()
    {
        \Log::info('migrate new domain --BEGIN--');

        \DB::enableQueryLog();

        $active_job = ActiveJob::where('table_name', '=', 'promotions_condition')->first();

        $querylog = \DB::getQueryLog();
        \Log::info($querylog);

        $last_id = !empty($active_job) ? $active_job->last_row : 0;
        $job = (new PromotionConditionNewRow($last_id));
        $this->dispatch($job);

        \Log::info('migrate new domain --FINISH--');
    }

    public function giftMigrate($id) {
        \Log::info('migrate voucher condition --BEGIN--');

        $continue = false;

        if($id == 0) {
            $active_job = ActiveJob::where('table_name', '=', 'gift')->first();
            $id = !empty($active_job) ? $active_job->last_row : 0;
            $continue = true;
        }

        $job = (new GiftMigrate($id, $continue));
        $this->dispatch($job);

        \Log::info('Begin migrate voucher code --FINISH--');
    }

    public function giftConditionMigrate($id) {
        \Log::info('migrate voucher condition --BEGIN--');

        $continue = false;

        if($id == 0) {
            $active_job = ActiveJob::where('table_name', '=', 'gift_condition')->first();
            $id = !empty($active_job) ? $active_job->last_row : 0;
            $continue = true;
        }

        $job = (new GiftConditionMigrate($id, $continue));
        $this->dispatch($job);

        \Log::info('Begin migrate voucher code --FINISH--');
    }

    public function giftConditionUpdate($id) {
        \Log::info('migrate voucher condition --BEGIN--');

        $continue = false;

        if($id == 0) {
            $active_job = ActiveJob::where('table_name', '=', 'gift_condition_update')->first();
            $id = !empty($active_job) ? $active_job->last_row : 0;
            $continue = true;
        }

        $job = (new GiftConditionUpdate($id, $continue));
        $this->dispatch($job);

        \Log::info('Begin migrate voucher code --FINISH--');
    }

    public function voucherUpdate($id) {
        $job = (new VoucherUpdate($id));
        $this->dispatch($job);
    }

    public function giftUpdate($id) {
        $job = (new GiftUpdate($id));
        $this->dispatch($job);
    }

    public function voucherConditionDelete($id) {
        $job = (new VoucherConditionDelete($id));
        $this->dispatch($job);
    }

    public function giftConditionDelete($id) {
        $job = (new GiftConditionDelete($id));
        $this->dispatch($job);
    }

    public function emptyVoucherCode() {
        $active_job = ActiveJob::where('table_name', '=', 'voucher_code_empty')->first();

        $last_id = !empty($active_job) ? $active_job->last_row : 0;
//        \Log::info($last_id);
        $job = (new MigrateEmptyVoucherCode($last_id));
        $this->dispatch($job);
    }

    public function updatePromo($id) {
        $continue = false;

        if($id == 0) {
            $active_job = ActiveJob::where('table_name', '=', 'update_promo')->first();
            $id = !empty($active_job) ? $active_job->last_row : 0;
            $continue = true;
        }

        $job = (new UpdatePromoParent($id, $continue));
        $this->dispatch($job);
    }

    public function voucherTemplateFix() {
        $active_job = ActiveJob::where('table_name', '=', 'update_promo')->first();

        $continue = false;

        if($id == 0) {
            $active_job = ActiveJob::where('table_name', '=', 'update_promo')->first();
            $id = !empty($active_job) ? $active_job->last_row : 0;
            $continue = true;
        }

        $job = (new VoucherTemplateFix($id, $continue));
        $this->dispatch($job);
    }
}
