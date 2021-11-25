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
            $table->bigInteger('id')->primary();
            $table->string('name')->index();

            $table->bigInteger('account_id');
            $table->uuid('db_fb_account_id');
            $table->bigInteger('campaign_id');
            $table->uuid('team_id');
            $table->uuid('user_id');

            $table->foreign('campaign_id')
                ->references('id')
                ->on('fb_account_campaigns');

            $table->foreign('account_id')
                ->references('account_id')
                ->on('fb_ad_accounts')
                ->onDelete('cascade');

            $table->foreign('db_fb_account_id')
                ->references('id')
                ->on('fb_accounts');

            $table->foreign('team_id')
                ->references('id')
                ->on('teams');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->enum('status', [
                'ACTIVE',
                'PAUSED',
                'DELETED',
                'ARCHIVED'
            ])->index();

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
            ])->index();

            $table->decimal('daily_budget')->index();
            $table->decimal('lifetime_budget')->index();
            $table->decimal('budget_remaining')->index();

            $table->enum('bid_strategy', [
                'LOWEST_COST_WITHOUT_CAP',
                'LOWEST_COST_WITH_BID_CAP',
                'COST_CAP'
            ])->nullable()->index();

            $table->decimal('bid_amount')->nullable()->index();

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
