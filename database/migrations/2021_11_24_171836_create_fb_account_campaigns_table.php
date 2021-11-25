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
            $table->bigInteger('id')->primary();
            $table->string('name')->index();

            $table->bigInteger('account_id');
            $table->uuid('db_fb_account_id');
            $table->uuid('team_id');
            $table->uuid('user_id');

            $table->foreign('account_id')
                ->references('account_id')
                ->on('fb_ad_accounts')
                ->onDelete('cascade');

            $table->foreign('db_fb_account_id')
                ->references('id')
                ->on('fb_accounts')
                ->onDelete('cascade');

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
                'ARCHIVED',
                'IN_PROCESS',
                'WITH_ISSUES'
            ])->index();

            $table->string('daily_budget')->nullable()->index();
            $table->string('lifetime_budget')->nullable()->index();
            $table->decimal('budget_remaining')->index();
            $table->string('objective')->index();

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
        Schema::dropIfExists('fb_account_campaigns');
    }
}
