<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbAccountAdsetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_account_adsets', function (Blueprint $table) {
            $table->bigInteger('id')->unique()->unsigned();
            $table->string('name');
            $table->bigInteger('account_id');
            $table->uuid('db_fb_account_id');
            $table->bigInteger('campaign_id');
            $table->uuid('team_id');
            $table->uuid('user_id');
            $table->enum('status', [
                'ACTIVE',
                'PAUSED',
                'DELETED',
                'ARCHIVED'
            ]);

            $table->enum('effective_status', [
                'ACTIVE',
                'PAUSED',
                'DELETED',
                'PENDING_REVIEW',
                'DISAPPROVED',
                'PREAPPROVED',
                'PENDING_BILLING_INFO',
                'CAMPAIGN_PAUSED',
                'ARCHIVED',
                'ADSET_PAUSED',
                'IN_PROCESS',
                'WITH_ISSUES'
            ]);

            $table->decimal('daily_budget');
            $table->decimal('lifetime_budget');
            $table->decimal('budget_remaining');

            $table->enum('bid_strategy', [
                'LOWEST_COST_WITHOUT_CAP',
                'LOWEST_COST_WITH_BID_CAP',
                'COST_CAP'
            ])->nullable();

            $table->decimal('bid_amount')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fb_account_adsets');
    }
}
