<?php

namespace Tests\Feature;

use App\Models\Proxy;
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

        $response = $this->post('/api/fb-accounts', $data, $this->headers);

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

        $response = $this->post('/api/fb-accounts', $data, $this->headers);

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

        $response = $this->post('/api/fb-accounts', $data, $this->headers);

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

        $response = $this->post('/api/fb-accounts', $data, $this->headers);

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

        $response = $this->post('/api/fb-accounts', $data, $this->headers);

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

    public function test_success_with_new_proxy()
    {
        $data = [
            'name'         => 'accountWithNewProxy',
            'access_token' => $this->faker->text(100),
            'proxy'        => [
                'type'     => 'http',
                'host'     => '1.1.1.1',
                'port'     => 8080,
                'name'     => 'newProxyForAccount',
                'login'    => 'login',
                'password' => 'password'
            ]
        ];

        $response = $this->post('/api/fb-accounts', $data, $this->headers);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'team_id',
                'name',
                'access_token',
                'proxy_id'
            ]
        ]);
        $this->assertEquals(true, !is_null($response->json()['data']['proxy_id']));

        $newProxy = Proxy::find($response->json()['data']['proxy_id']);
        $this->assertEquals('1.1.1.1', $newProxy->host);
        $this->assertEquals(8080, $newProxy->port);
        $this->assertEquals('http', $newProxy->type);
        $this->assertEquals('newProxyForAccount', $newProxy->name);
        $this->assertEquals('login', $newProxy->login);
        $this->assertEquals('password', $newProxy->password);
    }
}
