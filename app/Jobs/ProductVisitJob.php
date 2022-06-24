<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use \App\Modules\Product\Models\ProductVisit;

class ProductVisitJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ProductVisit::insertVisit($this->data);
        \Log::info('insert product visit : '. json_encode($this->data));
    }
}
