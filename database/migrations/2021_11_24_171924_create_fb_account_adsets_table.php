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
