<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbCreativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_creatives', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('creative_id')->index();

            $table->uuid('team_id');
            $table->uuid('user_id');
            $table->uuid('fb_ad_account_id');

            $table->foreign('fb_ad_account_id')
                ->references('id')
                ->on('fb_ad_accounts')
                ->onDelete('cascade');

            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->string('effective_instagram_story_id')->index();
            $table->string('effective_object_story_id')->index();
            $table->string('instagram_permalink_url')->index();
            $table->text('object_story_spec')->nullable();

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
        Schema::dropIfExists('fb_creatives');
    }
}
