<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::query()->get();
        $ids = $users->pluck('id');
        $emails = $users->pluck('username');
        return [
            //
            'id' => Str::uuid()->toString(),
            'founder_id' => $this->faker->randomElement($ids),
            'name' => $this->faker->randomElement($emails)
        ];
    }
}
