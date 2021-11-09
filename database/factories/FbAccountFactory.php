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
        $user = User::query()->first();
        $statuses = ['NEW', 'TOKEN_ERROR', 'ACTIVE'];
        return [
            //
            'user_id' => $user->id,
            'team_id' => $user->team_id,
            'name' => $this->faker->name,
            'access_token' => $this->faker->word,
            'status' => $this->faker->randomElement($statuses)
        ];
    }
}
