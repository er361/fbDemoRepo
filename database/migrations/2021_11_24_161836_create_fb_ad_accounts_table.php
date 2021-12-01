<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbAdAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_ad_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('ad_account_id')->index();

            $table->uuid('fb_account_id');
            $table->uuid('team_id');
            $table->uuid('user_id');

            $table->foreign('fb_account_id')
                ->references('id')
                ->on('fb_accounts');

            $table->foreign('team_id')
                ->references('id')
                ->on('teams');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->string('name')->index();
            $table->float('amount_spent')->index();

            $table->integer('account_status')->index();

            $table->string('balance')->index();
            $table->string('business_city')->index();
            $table->string('business_country_code')->index();
            $table->string('business_name')->index();
            $table->string('business_street')->index();
            $table->string('business_street2')->index();
            $table->boolean('is_notifications_enabled');
            $table->string('currency')->index();
            $table->integer('disable_reason')->index();
            $table->json('funding_source_details')->index()->nullable();
            $table->integer('is_personal')->index();
            $table->integer('timezone_offset_hours_utc')->index();
            $table->integer('adtrust_dsl')->index();

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
        Schema::dropIfExists('fb_ad_accounts');
    }
}
