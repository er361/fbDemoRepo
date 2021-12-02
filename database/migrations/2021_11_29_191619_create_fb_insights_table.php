<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbInsightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_insights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('fb_account_id');

            $table->bigInteger('object_id')->index();

            $table->enum('level', [
                'adAccount',
                'ad',
                'campaign',
                'adset'
            ])->index();

            $table->date('date')->index();
            $table->bigInteger('impressions')->index();
            $table->float('spend')->index();

            $table->uuid('team_id');
            $table->uuid('user_id');

            $table->foreign('fb_account_id')
                ->references('id')
                ->on('fb_accounts')
                ->onDelete('cascade');

            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

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
        Schema::dropIfExists('fb_insights');
    }
}
