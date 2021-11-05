<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('team_id');
            $table->uuid('proxy_id')->nullable();
            $table->string('name')->index();

            $table->text('notes')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('access_token');
            $table->text('access_token_error_message')->nullable();
            $table->text('business_access_token')->nullable();
            $table->text('fbdtsg')->nullable();

            $table->string('lsd')->index()->nullable();
            $table->string('login')->index()->nullable();
            $table->string('password')->index()->nullable();

            $table->text('cookies')->nullable();

            $table->enum('status', ['NEW', 'TOKEN_ERROR', 'ACTIVE'])
                ->default('NEW');

            $table->boolean('activity_block')->default(false);
            $table->boolean('archived')->default(false);
            $table->string('facebook_id')->index()->nullable();
            $table->string('facebook_profile_name')->index()->nullable();

            $table->boolean('advertising_rules_accepted')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('team_id')
                ->references('id')
                ->on('teams');

            $table->foreign('proxy_id')
                ->references('id')
                ->on('proxy');
        });

        DB::statement('alter table fb_accounts TRANSACTIONAL=0');

        DB::statement('alter table fb_accounts ADD FULLTEXT INDEX idx_ft_notes (notes)');
        DB::statement('alter table fb_accounts ADD FULLTEXT INDEX idx_ft_acc_token (access_token)');
        DB::statement('alter table fb_accounts ADD FULLTEXT INDEX idx_ft_business_token (business_access_token)');
        DB::statement('alter table fb_accounts ADD FULLTEXT INDEX idx_ft_cookies (cookies)');
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
