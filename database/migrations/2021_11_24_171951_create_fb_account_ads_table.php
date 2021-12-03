<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbAccountAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_ads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('ad_id')->index();

            $table->bigInteger('account_id');
            $table->bigInteger('campaign_id');
            $table->bigInteger('adset_id');

            $table->uuid('team_id');
            $table->uuid('user_id');

            $table->uuid('fb_ad_account_id');
            $table->uuid('fb_campaign_id');
            $table->uuid('fb_adset_id');

            $table->foreign('fb_ad_account_id')
                ->references('id')
                ->on('fb_ad_accounts')
                ->onDelete('cascade');

            $table->foreign('fb_campaign_id')
                ->references('id')
                ->on('fb_campaigns')
                ->onDelete('cascade')
                ->onUpdate('cascade');


            $table->foreign('fb_adset_id')
                ->references('id')
                ->on('fb_adsets')
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

            $table->decimal('daily_budget')->nullable()->index();
            $table->decimal('lifetime_budget')->nullable()->index();
            $table->decimal('budget_remaining')->nullable()->index();
            $table->json('ad_review_feedback')->nullable()->index();
            $table->string('creative_id')->index();
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
        Schema::dropIfExists('fb_ads');
    }
}
