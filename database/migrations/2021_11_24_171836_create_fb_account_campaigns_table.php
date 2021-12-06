<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbAccountCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('campaign_id')->index();
            $table->bigInteger('account_id');

            $table->uuid('fb_ad_account_id');
            $table->uuid('team_id');
            $table->uuid('user_id');


            $table->foreign('fb_ad_account_id')
                ->references('id')
                ->on('fb_ad_accounts')
                ->onDelete('cascade');

            $table->foreign('team_id')
                ->references('id')
                ->on('teams');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->string('name')->index();
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
                'ARCHIVED',
                'IN_PROCESS',
                'WITH_ISSUES'
            ])->index();

            $table->string('daily_budget')->nullable()->index();
            $table->string('lifetime_budget')->nullable()->index();
            $table->decimal('budget_remaining')->index();
            $table->enum('objective', [
                'APP_INSTALLS',
                'CONVERSIONS',
                'EVENT_RESPONSES',
                'LEAD_GENERATION',
                'LINK_CLICKS',
                'MESSAGES',
                'PAGE_LIKES',
                'POST_ENGAGEMENT',
                'VIDEO_VIEWS'
            ])->index();

            $table->enum('bid_strategy', [
                'LOWEST_COST_WITHOUT_CAP',
                'LOWEST_COST_WITH_BID_CAP',
                'COST_CAP'
            ])->nullable()->index();

            $table->string('bid_amount')->nullable()->index();

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
        Schema::dropIfExists('fb_campaigns');
    }
}
