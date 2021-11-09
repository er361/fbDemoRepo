<?php

namespace Database\Seeders;

use App\Models\FbAccount;
use Illuminate\Database\Seeder;

class FbAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        FbAccount::factory()->count(5)->create();
    }
}
