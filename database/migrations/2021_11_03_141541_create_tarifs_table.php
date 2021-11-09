<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTarifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tarifs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->enum('plan', ['alpha', 'beta', 'trial', 'base', 'pro'])->default('alpha');
            $table->unsignedInteger('users_limit')->default(3);
            $table->timestamps();

//            $table->foreign('user_id')
//                ->references('id')
//                ->on('users');
        });

        Schema::table('tarifs', function (Blueprint $table) {
//            $table->foreign('user_id')
//                ->references('id')
//                ->on('users');
        });
//        DB::statement('alter table tarifs TRANSACTIONAL=1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tarifs');
    }
}
