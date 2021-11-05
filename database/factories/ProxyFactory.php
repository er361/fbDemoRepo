<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProxyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ids = User::query()->pluck('id');
        $type = ['http', 'socks5'];
        $status = ['new', 'active', 'error'];
        $permissions[] = $this->faker->randomElement($ids) . '-' . 'usage';

        return [
            //
            'user_id' => '84ba7134-a75d-428a-9195-0bafde28964a',
            'team_id' => '756456f6-0b7d-435b-81c8-5abfcb2a5ff7',
            'type' => $this->faker->randomElement($type),
            'name' => $this->faker->name,
//            'permissions' => $permissions,
            'host' => $this->faker->ipv4(),
            'port' => $this->faker->randomNumber(5),
            'login' => $this->faker->userName(),
            'password' => $this->faker->password(3, 5),
            'status' => $this->faker->randomElement($status),
            'external_ip' => $this->faker->ipv4,
//            'last_check' => $this->faker->dateTimeThisDecade()->getTimestamp()
        ];
    }
}
