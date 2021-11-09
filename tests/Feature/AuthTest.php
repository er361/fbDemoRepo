<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group auth
 */
class AuthTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function test_refresh_token()
    {
        $loginResponse = $this->post('/api/auth/login', ['username' => 'cloud@dolphin.ru.com', 'password' => 'password']);
        $headers = ['Authorization' => 'Bearer ' . $loginResponse->json()['data']['access_token']];

        $response = $this->post('/api/auth/refresh-token', [], $headers);
        $response->assertJsonStructure([
            'data' => [
                'access_token',
                'user'
            ]
        ]);

        $response->assertStatus(200);
    }

    public function test_logout()
    {
        $loginResponse = $this->post('/api/auth/login', ['username' => 'cloud@dolphin.ru.com', 'password' => 'password']);
        $headers = ['Authorization' => 'Bearer ' . $loginResponse->json()['data']['access_token']];

        $response = $this->post('/api/auth/logout', [], $headers);
        $response->assertStatus(200);

        $profileResponse = $this->get('/api/profile', $headers);
        $profileResponse->assertStatus(401);
    }

    public function test_get_profile()
    {
        $loginResponse = $this->post('/api/auth/login', ['username' => 'cloud@dolphin.ru.com', 'password' => 'password']);
        $headers = ['Authorization' => 'Bearer ' . $loginResponse->json()['data']['access_token']];

        $profileResponse = $this->get('/api/profile', $headers);
        $profileResponse->assertStatus(200);
    }
}
