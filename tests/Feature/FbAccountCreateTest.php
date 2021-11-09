<?php

namespace Tests\Feature;

use App\Models\Proxy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group accounts
 */
class FbAccountCreateTest extends TestCase
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

    public function test_success_with_new_proxy_with_no_auth_and_name()
    {
        $data = [
            'name'         => 'accountWithNewProxy',
            'access_token' => $this->faker->text(100),
            'proxy'        => [
                'type' => 'http',
                'host' => '1.1.1.1',
                'port' => 8080,
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
    }

    public function test_success_with_new_proxy_and_proxy_id()
    {
        $someProxy = Proxy::first();

        $data = [
            'name'         => 'accountWithNewProxy',
            'access_token' => $this->faker->text(100),
            'proxy_id'     => $someProxy->id,
            'proxy'        => [
                'type' => 'http',
                'host' => '1.1.1.1',
                'port' => 8080,
            ]
        ];

        $response = $this->post('/api/fb-accounts', $data, $this->headers);

        $response->assertStatus(422);
    }

    public function test_success_with_business_access_token()
    {
        $data = [
            'name'                  => $this->faker->name(),
            'access_token'          => $this->faker->text(100),
            'business_access_token' => $this->faker->text(100),

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
                'business_access_token'
            ]
        ]);
        $response->assertJsonPath(
            'data.business_access_token',
            $data['business_access_token']
        );
    }

    public function test_success_with_login()
    {
        $data = [
            'name'         => $this->faker->name(),
            'access_token' => $this->faker->text(100),
            'login'        => $this->faker->word(),

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
                'login'
            ]
        ]);
        $response->assertJsonPath(
            'data.login',
            $data['login']
        );
    }

    public function test_success_with_password()
    {
        $data = [
            'name'         => $this->faker->name(),
            'access_token' => $this->faker->text(100),
            'password'     => $this->faker->word(),

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
                'password'
            ]
        ]);
        $response->assertJsonPath(
            'data.password',
            $data['password']
        );
    }

    public function test_success_with_user_agent()
    {
        $data = [
            'name'         => $this->faker->name(),
            'access_token' => $this->faker->text(100),
            'user_agent'   => $this->faker->chrome(),

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
                'user_agent'
            ]
        ]);
        $response->assertJsonPath(
            'data.user_agent',
            $data['user_agent']
        );
    }

    public function test_success_with_cookies()
    {
        $data = [
            'name'         => $this->faker->name(),
            'access_token' => $this->faker->text(100),
            'cookies'      => '[{"domain":".google.com","expirationDate":1698138255.826775,"hostOnly":false,"httpOnly":false,"name":"__Secure-1PAPISID","path":"/","sameSite":"unspecified","secure":true,"session":false,"storeId":"0","value":"OAtxkISrVAnw6pNm/AbyZY_PR2vN3rzhNR","id":1},{"domain":".google.com","expirationDate":1698138255.825725,"hostOnly":false,"httpOnly":true,"name":"__Secure-1PSID","path":"/","sameSite":"unspecified","secure":true,"session":false,"storeId":"0","value":"DQhEDc5tyk91kwcUOmAg19hyza9I85zxv6DM3wIOrsAQ0JqBptswE30pU7qjN9WDrjK4FQ.","id":2},{"domain":".google.com","expirationDate":1698138255.826913,"hostOnly":false,"httpOnly":false,"name":"__Secure-3PAPISID","path":"/","sameSite":"no_restriction","secure":true,"session":false,"storeId":"0","value":"OAtxkISrVAnw6pNm/AbyZY_PR2vN3rzhNR","id":3},{"domain":".google.com","expirationDate":1698138255.825973,"hostOnly":false,"httpOnly":true,"name":"__Secure-3PSID","path":"/","sameSite":"no_restriction","secure":true,"session":false,"storeId":"0","value":"DQhEDc5tyk91kwcUOmAg19hyza9I85zxv6DM3wIOrsAQ0JqBfi7OiPDVGG1B5Pj8UX280Q.","id":4},{"domain":".google.com","expirationDate":1667999469.073182,"hostOnly":false,"httpOnly":true,"name":"__Secure-3PSIDCC","path":"/","sameSite":"no_restriction","secure":true,"session":false,"storeId":"0","value":"AJi4QfFDSn74ml4tJH3GWV6FWO_MnwzVxzK0ug663rpvubhhBVx-xKMHi0WZTVl6Qn61Q5YR7-QZ","id":5},{"domain":".google.com","expirationDate":1639055468.884027,"hostOnly":false,"httpOnly":false,"name":"1P_JAR","path":"/","sameSite":"no_restriction","secure":true,"session":false,"storeId":"0","value":"2021-11-09-13","id":6},{"domain":".google.com","expirationDate":1698138255.826437,"hostOnly":false,"httpOnly":false,"name":"APISID","path":"/","sameSite":"unspecified","secure":false,"session":false,"storeId":"0","value":"aAyf4pXIlb77bFnb/AUQ_obibKPWWsTie7","id":7},{"domain":".google.com","expirationDate":2145916800.818277,"hostOnly":false,"httpOnly":false,"name":"CONSENT","path":"/","sameSite":"unspecified","secure":true,"session":false,"storeId":"0","value":"PENDING+557","id":8},{"domain":".google.com","expirationDate":1698138255.826141,"hostOnly":false,"httpOnly":true,"name":"HSID","path":"/","sameSite":"unspecified","secure":false,"session":false,"storeId":"0","value":"APZXLKaV8RqBh7IPM","id":9},{"domain":".google.com","expirationDate":1652274667.632148,"hostOnly":false,"httpOnly":true,"name":"NID","path":"/","sameSite":"no_restriction","secure":true,"session":false,"storeId":"0","value":"511=t0p8VPzUlfckPEtMwXM6c1FkhFwZ69xzqM_wQbAWqEWX3mM9uFA6V25xMRqYPoyCecgdmmsscDhkyHuX2hCTmxA7Qb0M8Va573e1wyuxPvGd2xFTI_qE4BdmOvmJZjEQjgkpunerzsHI5RqjBKm3m7dvZ1HYsalM8jQme0heqbOW0lurKLX1RSTEyETceakCnW4ubN1a4dGTppSiLQKpnoeOGLgZKamsu8qi1KjCPL0jUiZXOGASyFsYkiK9o86l1gTzIoJID0O8OD88_kYc7rFpdM_pMfk_0aziLkLYbFDaOtgjf-wuU8GDjxSL8YnjGZSarPrl4KcW5GcGZ055w0Mj","id":10},{"domain":".google.com","expirationDate":1639055467,"hostOnly":false,"httpOnly":false,"name":"OGPC","path":"/","sameSite":"unspecified","secure":false,"session":false,"storeId":"0","value":"19025836-2:19022519-1:","id":11},{"domain":".google.com","expirationDate":1698138255.826589,"hostOnly":false,"httpOnly":false,"name":"SAPISID","path":"/","sameSite":"unspecified","secure":true,"session":false,"storeId":"0","value":"OAtxkISrVAnw6pNm/AbyZY_PR2vN3rzhNR","id":12},{"domain":".google.com","expirationDate":1648567189.798205,"hostOnly":false,"httpOnly":false,"name":"SEARCH_SAMESITE","path":"/","sameSite":"strict","secure":false,"session":false,"storeId":"0","value":"CgQI1JMB","id":13},{"domain":".google.com","expirationDate":1698138255.825344,"hostOnly":false,"httpOnly":false,"name":"SID","path":"/","sameSite":"unspecified","secure":false,"session":false,"storeId":"0","value":"DQhEDc5tyk91kwcUOmAg19hyza9I85zxv6DM3wIOrsAQ0JqBWNo9s1imnU0R1QV2pHAPRQ.","id":14},{"domain":".google.com","expirationDate":1667999469.073148,"hostOnly":false,"httpOnly":false,"name":"SIDCC","path":"/","sameSite":"unspecified","secure":false,"session":false,"storeId":"0","value":"AJi4QfHWsf3ZYgQnNDZb5JeyYX08S_UHkYMet8ECP9KB3rqQvP1rdW5LsB7GZjQUx8pcbrz9MBwb","id":15},{"domain":".google.com","expirationDate":1698138255.826287,"hostOnly":false,"httpOnly":true,"name":"SSID","path":"/","sameSite":"unspecified","secure":true,"session":false,"storeId":"0","value":"AUKMubKJYiuO1QT8Q","id":16},{"domain":"www.google.com","expirationDate":1636801490,"hostOnly":true,"httpOnly":false,"name":"OTZ","path":"/","sameSite":"unspecified","secure":true,"session":false,"storeId":"0","value":"6198425_44_44_123780_40_436260","id":17}]',
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
                'cookies'
            ]
        ]);
        $response->assertJsonPath(
            'data.cookies',
            $data['cookies']
        );
    }
}
