<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FbAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::query()
            ->where('username', 'cloud@dolphin.ru.com')
            ->first();
        $user2 = User::query()->where('username', '<>', 'cloud@dolphin.ru.com')
            ->first();
        $userIds = [$user->id, $user2->id];

        $statuses = ['NEW', 'TOKEN_ERROR', 'ACTIVE'];
        return [
            //
            'user_id' => $this->faker->randomElement($userIds),
            'team_id' => $user->team_id,
            'name' => $this->faker->name,
            'access_token' => $this->faker->word,
            'status' => $this->faker->randomElement($statuses),
            'facebook_id' => rand(1000000, 9999999),
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36'
        ];
    }
}
