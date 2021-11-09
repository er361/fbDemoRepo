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
        return [
            'user_id'      => $user->id,
            'team_id'      => $user->team_id,
            'name'         => 'fake / ' . $this->faker->name,
            'access_token' => $this->faker->word,
            'status'       => 'ACTIVE',
            'facebook_id'  => rand(1000000, 9999999),
            'user_agent'   => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36'
        ];
    }
}
