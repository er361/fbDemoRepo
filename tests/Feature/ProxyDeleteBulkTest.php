<?php

namespace Tests\Feature;

use App\Models\Proxy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group proxy
 */
class ProxyDeleteBulkTest extends TestCase
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

    public function test_successful_delete()
    {
        // взять два специально заготовленных прокси
        $proxyToDelete1 = Proxy::where('name', 'proxyToDelete1')
            ->select('id')
            ->first();
        $proxyToDelete2 = Proxy::where('name', 'proxyToDelete2')
            ->select('id')
            ->first();

        // удалить их
        $response = $this->delete(
            '/api/proxy/delete-bulk',
            ['ids' => [$proxyToDelete1->id, $proxyToDelete2->id]],
            $this->headers
        );
        $response->assertStatus(200);

        // снова взять их из БД
        $proxyToDelete1 = Proxy::where('name', 'proxyToDelete1')
            ->first();
        $proxyToDelete2 = Proxy::where('name', 'proxyToDelete2')
            ->first();

        // проверить факт их удаления
        $this->assertEquals(true, is_null($proxyToDelete1->deleted_at));
        $this->assertEquals(true, is_null($proxyToDelete2->deleted_at));

        // проверить ответ API
        $response->assertJsonPath('success', true);
    }
}
