<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbAccountAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_apps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('app_id')->index();

            $table->uuid('fb_ad_account_id');

            $table->uuid('team_id');
            $table->uuid('user_id');

            $table->string('name')->index();
            $table->text('logo_url');
            $table->text('supported_platforms');
            $table->text('object_store_urls');
            $table->timestamps();

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
        });

        DB::statement('alter table fb_apps ADD FULLTEXT INDEX idx_ft_logo_url (logo_url)');
        DB::statement('alter table fb_apps ADD FULLTEXT INDEX idx_ft_supported_platforms (supported_platforms)');
        DB::statement('alter table fb_apps ADD FULLTEXT INDEX idx_ft_object_store_urls (object_store_urls)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fb_apps');
    }
}
