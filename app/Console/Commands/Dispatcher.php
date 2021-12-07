<?php

namespace App\Console\Commands;

use App\Jobs\FbFetcherJob;
use App\Jobs\FetchAccountData;
use App\Jobs\OnDemandFetcherJob;
use App\Models\FbAccount;
use App\Models\OnDemand;
use Illuminate\Console\Command;

class Dispatcher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatcher:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $this->runOnDemand();
//
//        $this->runOnSchedule();
        $this->runTest();
        return Command::SUCCESS;
    }

    public function runTest()
    {
        FbAccount::withoutGlobalScopes()->each(function (FbAccount $account) {
            FetchAccountData::dispatch($account);
        });
    }

    public function runOnDemand(): void
    {
//        $this->withProgressBar(OnDemand::with('account')->get(), function ($onDemand) {
//            OnDemandFetcherJob::dispatch($onDemand->account)->onQueue('onDemand');
//        });
    }

    public function runOnSchedule(): void
    {
//        $subMinutes = now('Asia/Almaty')
//            ->subMinutes(env('SCHEDULE_ACCOUNTS_UPDATE_RATE'));
//
//        $accounts = FbAccount::where('updated_at', '<', $subMinutes)
//            ->get();
//
//        $this->withProgressBar($accounts, function (FbAccount $account) {
//            FbFetcherJob::dispatch($account)->onQueue('onSchedule');
//        });
    }
}
