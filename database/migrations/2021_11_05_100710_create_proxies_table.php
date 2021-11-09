<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProxiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('team_id')->index();
            $table->uuid('user_id')->index();
            $table->enum('type', ['http', 'https', 'socks4', 'socks5', 'ssh'])
                ->index();
            $table->string('name')->nullable();
            $table->string('host');
            $table->unsignedInteger('port');
            $table->string('login')->nullable();
            $table->string('password')->nullable();
            $table->text('change_ip_url')->nullable();
            $table->enum('status', ['new', 'active', 'error'])->index()->default('new');
            $table->string('external_ip')->nullable();
            $table->date('expiration_date')->index()->nullable();
            $table->timestamps();
            $table->softDeletes();

//            $table->foreign('user_id')
//                ->references('id')
//                ->on('users');
////
//            $table->foreign('team_id')
//                ->references('id')
//                ->on('teams');
        });

        DB::statement('alter table proxies ADD FULLTEXT INDEX idx_ft_name (name)');
        DB::statement('alter table proxies ADD FULLTEXT INDEX idx_ft_host (host)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proxies');
    }
}
