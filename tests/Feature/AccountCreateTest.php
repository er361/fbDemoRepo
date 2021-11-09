<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountCreateTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    private $headers;

    public function __construct()
    {
        parent::__construct();
        $user = 'cloud@dolphin.ru.com';
        $this->headers = [
            'Authorization' => self::AUTH_TOKENS[$user]
        ];
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_no_data()
    {
        $data = [
//            'name' => $this->faker->name(),
            'access_token' => $this->faker->text(100)
        ];

        $response = $this->post('/api/accounts', $data, $this->headers);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors'
        ]);
    }

    public function test_no_name()
    {
        $data = [
//            'name' => $this->faker->name(),
            'access_token' => $this->faker->text(100)
        ];

        $response = $this->post('/api/accounts', $data, $this->headers);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'name',
            ]
        ]);
    }

    public function test_no_access_token()
    {
        $data = [
            'name' => $this->faker->name(),
//            'access_token' => $this->faker->text(100)
        ];

        $response = $this->post('/api/accounts', $data, $this->headers);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'access_token',
            ]
        ]);
    }

    public function test_success()
    {
        $data = [
            'name'         => $this->faker->name(),
            'access_token' => $this->faker->text(100)
        ];

        $response = $this->post('/api/accounts', $data, $this->headers);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'team_id',
                'name',
                'access_token',
            ]
        ]);
    }

    public function test_success_with_tags()
    {
        $data = [
            'name'         => $this->faker->name(),
            'access_token' => $this->faker->text(100),
            'tags'         => [$this->faker->word(), $this->faker->word()]
        ];

        $response = $this->post('/api/accounts', $data, $this->headers);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'team_id',
                'name',
                'access_token',
                'tags'
            ]
        ]);
    }
}
