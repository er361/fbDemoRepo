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
        Schema::create('fb_account_campaigns', function (Blueprint $table) {
            $table->bigInteger('id')->unique()->unsigned();
            $table->string('name');
            $table->bigInteger('account_id');
            $table->uuid('db_fb_account_id');
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
                'ARCHIVED',
                'IN_PROCESS',
                'WITH_ISSUES'
            ]);

            $table->string('daily_budget')->nullable();
            $table->string('lifetime_budget')->nullable();
            $table->decimal('budget_remaining');
            $table->string('objective');

            $table->enum('bid_strategy', [
                'LOWEST_COST_WITHOUT_CAP',
                'LOWEST_COST_WITH_BID_CAP',
                'COST_CAP'
            ])->nullable();

            $table->string('bid_amount')->nullable();

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
        Schema::dropIfExists('fb_account_campaigns');
    }
}
