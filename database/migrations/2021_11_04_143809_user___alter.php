<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserAlter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('email');
            $table->dropColumn('email_verified_at');
            $table->dropColumn('remember_token');
            $table->softDeletes()->after('updated_at');
            $table->uuid('team_id')->nullable(false)->after('id');
            $table->string('username')->after('team_id')->unique();
        });
    }

    /**
     * Reverse the migrations.
     * те
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('email')->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->rememberToken()->after('email_verified_at');
            $table->dropColumn('team_id');
            $table->dropColumn('username');
            $table->dropColumn('deleted_at');
        });
    }
}
