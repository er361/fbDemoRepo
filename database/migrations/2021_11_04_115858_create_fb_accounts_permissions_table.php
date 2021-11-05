<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFbAccountsPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_accounts_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_id');
            $table->uuid('team_id');
            $table->enum('name',[
                'read','update','delete','stat'
            ]);
            $table->timestamps();

            $table->foreign('account_id')
                ->references('id')
                ->on('fb_accounts');

            $table->foreign('team_id')
                ->references('id')
                ->on('teams');
        });

        DB::statement('alter table fb_accounts_permissions TRANSACTIONAL=0');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fb_accounts_permissions');
    }
}
