<?php

namespace Database\Seeders;

use App\Models\Proxy;
use Illuminate\Database\Seeder;

class ProxySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Proxy::factory()->count(5)->create();
    }
}
