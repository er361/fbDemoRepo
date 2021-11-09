<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_no_username()
    {
        $data = [
            'password' => $this->faker->password(5)
        ];
        $response = $this->post('/api/auth/register', $data);

        $response->assertStatus(422);
    }

    public function test_no_password()
    {
        $data = [
            'username' => $this->faker->email()
        ];
        $response = $this->post('/api/auth/register', $data);

        $response->assertStatus(422);
    }

    public function test_short_password()
    {
        $data = [
            'username' => $this->faker->email(),
            'password' => $this->faker->password(1, 4)
        ];
        $response = $this->post('/api/auth/register', $data);

        $response->assertStatus(422);
    }

    public function test_success()
    {
        $data = [
            'username' => $this->faker->email(),
            'password' => $this->faker->password(5)
        ];
        $response = $this->post('/api/auth/register', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'team_id',
                'username'
            ]
        ]);
        $response->assertJsonPath('data.username', $data['username']);
    }

    public function test_success_with_display_name()
    {
        $data = [
            'username'     => $this->faker->email(),
            'password'     => $this->faker->password(5),
            'display_name' => $this->faker->name()
        ];
        $response = $this->post('/api/auth/register', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'team_id',
                'username'
            ]
        ]);
        $response->assertJsonPath('data.username', $data['username']);
        $response->assertJsonPath('data.display_name', $data['display_name']);
    }
}
