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
        Schema::create('fb_account_ads', function (Blueprint $table) {
            $table->bigInteger('id')->unique()->unsigned();
            $table->string('name');
            $table->bigInteger('account_id');
            $table->uuid('db_fb_account_id');
            $table->bigInteger('campaign_id');
            $table->bigInteger('adset_id');
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

            $table->decimal('daily_budget')->nullable();
            $table->decimal('lifetime_budget')->nullable();
            $table->decimal('budget_remaining')->nullable();
            $table->json('ad_review_feedback')->nullable();
            $table->string('creative_id');
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
        Schema::dropIfExists('fb_account_ads');
    }
}
