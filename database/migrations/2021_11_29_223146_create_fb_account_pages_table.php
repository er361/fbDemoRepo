<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbAccountPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fb_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('page_id')->index();
            $table->uuid('fb_account_id');

            $table->text('access_token')->index();
            $table->boolean('is_published');
            $table->text('picture');
            $table->string('name')->index();
            $table->string('category')->index();

            $table->text('category_list');
            $table->text('tasks');
            $table->text('cover')->nullable();

            $table->uuid('team_id');
            $table->uuid('user_id');

            $table->foreign('fb_account_id')
                ->references('id')
                ->on('fb_accounts')
                ->onDelete('cascade');

            $table->foreign('team_id')
                ->references('id')
                ->on('teams')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->timestamps();
        });

        DB::statement('alter table fb_pages ADD FULLTEXT INDEX idx_ft_picture (picture)');
        DB::statement('alter table fb_pages ADD FULLTEXT INDEX idx_ft_categry_list (category_list)');
        DB::statement('alter table fb_pages ADD FULLTEXT INDEX idx_ft_tasks (tasks)');
        DB::statement('alter table fb_pages ADD FULLTEXT INDEX idx_ft_cover (cover)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fb_pages');
    }
}
