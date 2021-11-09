<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_no_data()
    {
        $response = $this->post('/api/auth/login');
        $response->assertStatus(422);
    }

    public function test_no_password()
    {
        $response = $this->post('/api/auth/login', ['username' => $this->faker()->email()]);
        $response->assertStatus(422);
    }

    public function test_no_username()
    {
        $response = $this->post('/api/auth/login', ['password' => $this->faker->password(5)]);
        $response->assertStatus(422);
    }

    public function test_success()
    {
        $response = $this->post('/api/auth/login', ['username' => 'cloud@dolphin.ru.com', 'password' => 'password']);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'access_token',
                'user'
            ]
        ]);
    }
}
