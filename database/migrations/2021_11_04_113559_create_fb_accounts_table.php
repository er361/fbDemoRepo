<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_accounts', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id');
            $table->string('name');
            $table->text('notes')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('access_token');
            $table->text('business_access_token')->nullable();
            $table->text('fbdtsg')->nullable();
            $table->string('lsd')->nullable();
            $table->string('login')->nullable();
            $table->string('password')->nullable();
            $table->text('cookies')->nullable();
            $table->uuid('status')->nullable();
            $table->boolean('activity_block')->default(false);
            $table->boolean('archived')->default(false);
            $table->string('facebook_id')->nullable();
            $table->string('facebook_profile_name')->nullable();
            $table->boolean('advertising_rules_accepted')->default(false);
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
        Schema::dropIfExists('fb_accounts');
    }
}
