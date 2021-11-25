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
            $table->string('id');
            $table->bigInteger('account_id');
            $table->uuid('db_fb_account_id');
            $table->uuid('team_id');
            $table->uuid('user_id');
            $table->string('name');
            $table->decimal('amount_spent');

            $table->integer('account_status');

            $table->decimal('balance');
            $table->string('business_city');
            $table->string('business_country_code');
            $table->string('business_name');
            $table->string('business_street');
            $table->string('business_street2');
            $table->boolean('is_notifications_enabled');
            $table->string('currency');
            $table->integer('disable_reason');
            $table->json('funding_source_details')->nullable();
            $table->integer('is_personal');
            $table->integer('timezone_offset_hours_utc');
            $table->integer('adtrust_dsl');
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
