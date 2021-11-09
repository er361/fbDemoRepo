<?php

namespace Database\Seeders;

use App\Models\Proxy;
use App\Models\User;
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
        $user = User::query()
            ->where('username', 'cloud@dolphin.ru.com')
            ->first();

        //
        Proxy::factory()->count(5)->create();

        // прокси для теста удаления
        Proxy::create([
            'team_id'  => $user->team_id,
            'user_id'  => $user->id,
            'name'     => 'proxyToDelete2',
            'status'   => 'active',
            'type'     => 'http',
            'host'     => '1.1.1.1',
            'port'     => '8080',
            'login'    => 'user',
            'password' => 'password',
        ]);
        Proxy::create([
            'team_id'  => $user->team_id,
            'user_id'  => $user->id,
            'name'     => 'proxyToDelete1',
            'status'   => 'active',
            'type'     => 'http',
            'host'     => '1.1.1.1',
            'port'     => '8080',
            'login'    => 'user',
            'password' => 'password',
        ]);
    }
}
