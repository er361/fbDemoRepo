<?php

namespace Database\Seeders;

use App\Models\FbAccount;
use App\Models\User;
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
        $user = User::query()
            ->where('username', 'cloud@dolphin.ru.com')
            ->first();

        // просто аккаунты :)
        FbAccount::factory()->count(5)->create();

        // аккаунт для теста архивации
        FbAccount::create([
            'team_id'      => $user->team_id,
            'user_id'      => $user->id,
            'name'         => 'accountToArchive',
            'access_token' => 'aaa',
            'status'       => 'ACTIVE'
        ]);

        // аккаунт для теста разархивации
        FbAccount::create([
            'team_id'      => $user->team_id,
            'user_id'      => $user->id,
            'name'         => 'accountToUnarchive',
            'access_token' => 'aaa',
            'status'       => 'ACTIVE',
            'archived'     => true
        ]);

        // аккаунты для теста удаления
        FbAccount::create([
            'team_id'      => $user->team_id,
            'user_id'      => $user->id,
            'name'         => 'accountToDelete2',
            'access_token' => 'aaa',
            'status'       => 'ACTIVE',
        ]);
        FbAccount::create([
            'team_id'      => $user->team_id,
            'user_id'      => $user->id,
            'name'         => 'accountToDelete1',
            'access_token' => 'aaa',
            'status'       => 'ACTIVE',
        ]);

        // аккаунт для теста редактирования
        FbAccount::create([
            'team_id'      => $user->team_id,
            'user_id'      => $user->id,
            'name'         => 'accountToUpdate',
            'access_token' => 'aaa',
            'status'       => 'ACTIVE',
            'archived'     => true
        ]);
    }
}
