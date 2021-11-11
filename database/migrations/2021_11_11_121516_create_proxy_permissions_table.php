<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProxyPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxy_permissions', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('proxy_id');
            $table->uuid('team_id');
            $table->uuid('from_user_id');
            $table->uuid('to_user_id');
            $table->enum('type', ['admin']);

            $table->foreign('proxy_id')
                ->references('id')
                ->on('proxies');

            $table->foreign('team_id')
                ->references('id')
                ->on('teams');

            $table->foreign('from_user_id')
                ->references('id')
                ->on('users');

            $table->foreign('to_user_id')
                ->references('id')
                ->on('users');

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
        Schema::dropIfExists('proxy_permissions');
    }
}
