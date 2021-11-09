<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TarifsAlter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('tarifs', 'teams_subscriptions');
        Schema::table('teams_subscriptions', function (Blueprint $table) {
//            $table->dropColumn('user_id');
            $table->uuid('team_id')->after('id')->index();
//            $table->dropIndex('tarifs_user_id_foreign');
//            $table->foreign('team_id')
//                ->references('id')
//                ->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('teams_subscriptions', 'tarifs');
        Schema::table('tarifs', function (Blueprint $table) {
            $table->integer('user_id')->after('id');
            $table->renameColumn('users_limit', 'usersLimit');
            $table->dropColumn('team_id');
        });
    }
}