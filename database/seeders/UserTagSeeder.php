<?php

namespace Database\Seeders;

use App\Models\UserTag;
use Database\Factories\UserTagFactory;
use Illuminate\Database\Seeder;

class UserTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        UserTag::factory()->count(10)->create();
    }
}
