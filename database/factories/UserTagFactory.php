<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserTagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ids = User::pluck('id');
        return [
            //
            'user_id' => $this->faker->randomElement($ids),
            'team_id' => '756456f6-0b7d-435b-81c8-5abfcb2a5ff7',
            'name' => $this->faker->word
        ];
    }
}
