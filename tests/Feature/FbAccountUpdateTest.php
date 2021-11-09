<?php

namespace Tests\Feature;

use App\Models\FbAccount;
use App\Models\Proxy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group accounts
 */
class FbAccountUpdateTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    public function __construct()
    {
        parent::__construct();
        $user = 'cloud@dolphin.ru.com';
        $this->headers = [
            'Authorization' => self::AUTH_TOKENS[$user]
        ];
    }

    public function test_update_name()
    {
        $accountToUpdate = FbAccount::where('name', 'accountToUpdate')
            ->first();

        $data = [
            'name' => $this->faker->name()
        ];

        $response = $this->put(
            "/api/fb-accounts/{$accountToUpdate->id}",
            $data,
            $this->headers
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', $data['name']);
    }

    public function test_update_new_proxy_with_auth()
    {
        $accountToUpdate = FbAccount::where('name', 'accountToUpdate')
            ->first();

        $data = [
            'proxy' => [
                'type'     => 'http',
                'host'     => '1.1.1.1',
                'port'     => 8080,
                'name'     => 'newProxyForAccount',
                'login'    => 'login',
                'password' => 'password'
            ]
        ];

        $response = $this->put(
            "/api/fb-accounts/{$accountToUpdate->id}",
            $data,
            $this->headers
        );

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json()['data']['proxy_id']);

        $newProxy = Proxy::find($response->json()['data']['proxy_id']);
        $this->assertEquals('1.1.1.1', $newProxy->host);
        $this->assertEquals(8080, $newProxy->port);
        $this->assertEquals('http', $newProxy->type);
        $this->assertEquals('newProxyForAccount', $newProxy->name);
        $this->assertEquals('login', $newProxy->login);
        $this->assertEquals('password', $newProxy->password);
    }

    public function test_update_new_proxy_without_auth_and_name()
    {
        $accountToUpdate = FbAccount::where('name', 'accountToUpdate')
            ->first();

        $data = [
            'proxy' => [
                'type' => 'http',
                'host' => '1.1.1.1',
                'port' => 8080,
            ]
        ];

        $response = $this->put(
            "/api/fb-accounts/{$accountToUpdate->id}",
            $data,
            $this->headers
        );

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json()['data']['proxy_id']);

        $newProxy = Proxy::find($response->json()['data']['proxy_id']);
        $this->assertEquals('1.1.1.1', $newProxy->host);
        $this->assertEquals(8080, $newProxy->port);
        $this->assertEquals('http', $newProxy->type);
    }

    public function test_update_user_agent()
    {
        $accountToUpdate = FbAccount::where('name', 'accountToUpdate')
            ->first();

        $data = [
            'user_agent' => $this->faker->chrome()
        ];

        $response = $this->put(
            "/api/fb-accounts/{$accountToUpdate->id}",
            $data,
            $this->headers
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.user_agent', $data['user_agent']);
    }

    public function test_update_access_token()
    {
        $accountToUpdate = FbAccount::where('name', 'accountToUpdate')
            ->first();

        $data = [
            'access_token' => $this->faker->word()
        ];

        $response = $this->put(
            "/api/fb-accounts/{$accountToUpdate->id}",
            $data,
            $this->headers
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.access_token', $data['access_token']);
    }

    public function test_update_business_access_token()
    {
        $accountToUpdate = FbAccount::where('name', 'accountToUpdate')
            ->first();

        $data = [
            'business_access_token' => $this->faker->word()
        ];

        $response = $this->put(
            "/api/fb-accounts/{$accountToUpdate->id}",
            $data,
            $this->headers
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.business_access_token', $data['business_access_token']);
    }

    public function test_update_notes()
    {
        $accountToUpdate = FbAccount::where('name', 'accountToUpdate')
            ->first();

        $data = [
            'notes' => $this->faker->text()
        ];

        $response = $this->put(
            "/api/fb-accounts/{$accountToUpdate->id}",
            $data,
            $this->headers
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.notes', $data['notes']);
    }

    public function test_update_proxy_id()
    {
        $accountToUpdate = FbAccount::where('name', 'accountToUpdate')
            ->first();
        $someProxy = Proxy::query()->first();

        $data = [
            'proxy_id' => $someProxy->id
        ];

        $response = $this->put(
            "/api/fb-accounts/{$accountToUpdate->id}",
            $data,
            $this->headers
        );

        $response->assertStatus(200);
        $response->assertJsonPath('data.proxy_id', $data['proxy_id']);
    }

    // использование proxy_id невозможно с использованием proxy и наоборот
    public function test_update_proxy_id_with_proxy_field()
    {
        $accountToUpdate = FbAccount::where('name', 'accountToUpdate')
            ->first();
        $someProxy = Proxy::query()->first();

        $data = [
            'proxy_id' => $someProxy->id,
            'proxy'    => [
                'type'     => 'http',
                'host'     => '1.1.1.1',
                'port'     => 8080,
                'name'     => 'newProxyForAccount',
                'login'    => 'login',
                'password' => 'password'
            ]
        ];

        $response = $this->put(
            "/api/fb-accounts/{$accountToUpdate->id}",
            $data,
            $this->headers
        );

        $response->assertStatus(422);
    }

    public function test_update_tags()
    {
        $accountToUpdate = FbAccount::where('name', 'accountToUpdate')
            ->first();

        $data = [
            'tags' => [$this->faker->word(), $this->faker->word()]
        ];

        $response = $this->put(
            "/api/fb-accounts/{$accountToUpdate->id}",
            $data,
            $this->headers
        );

        $response->assertStatus(200);
        $this->assertEquals(
            2,
            count($response->json()['data']['tags'])
        );
    }

    public function test_new_tags_should_replace_old_ones()
    {
        $accountToUpdate = FbAccount::where('name', 'accountToUpdate')
            ->first();

        // закидываем сначала один тег
        $data = [
            'tags' => ['first']
        ];

        $response = $this->put(
            "/api/fb-accounts/{$accountToUpdate->id}",
            $data,
            $this->headers
        );

        // потом другой тег
        $data = [
            'tags' => ['second']
        ];

        $response = $this->put(
            "/api/fb-accounts/{$accountToUpdate->id}",
            $data,
            $this->headers
        );

        // в результате на аккаунте должен остаться только тег `second`
        $response->assertStatus(200);
        $this->assertEquals(
            1,
            count($response->json()['data']['tags'])
        );
    }
}
