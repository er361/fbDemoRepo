<?php

namespace App\Jobs;

use App\Libraries\FbFetchBase;
use App\Models\FbAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchAccountData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected FbAccount $account;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(FbAccount $account)
    {
        //
        $this->account = $account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
//        $fbFetchBase = new FbFetchBase($this->account);
//        $fbFetchBase->process();
        sleep(10);
    }
}
