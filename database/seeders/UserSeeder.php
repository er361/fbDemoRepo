<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // cloud@dolphin.ru.com
        //
        DB::table('users')->insert([
            'username' => 'cloud@dolphin.ru.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'id'       => '84ba7134-a75d-428a-9195-0bafde28964a',
            'team_id'  => '756456f6-0b7d-435b-81c8-5abfcb2a5ff7',
        ]);

        DB::table('teams')->insert([
            'id'         => '756456f6-0b7d-435b-81c8-5abfcb2a5ff7',
            'name'       => 'cloud@dolphin.ru.com',
            'founder_id' => '84ba7134-a75d-428a-9195-0bafde28964a'
        ]);

        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'team_id'  => '756456f6-0b7d-435b-81c8-5abfcb2a5ff7',
                'username' => strtolower(Str::random(10)) . '@dolphin.ru.com',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
            ]);
        }
    }
}
