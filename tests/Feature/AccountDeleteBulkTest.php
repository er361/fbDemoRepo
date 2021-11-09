<?php

namespace Tests\Feature;

use App\Models\FbAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountDeleteBulkTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

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
        // взять два специально заготовленных аккаунтов
        $accountToDelete1 = FbAccount::where('name', 'accountToDelete1')
            ->select('id')
            ->first();
        $accountToDelete2 = FbAccount::where('name', 'accountToDelete2')
            ->select('id')
            ->first();

        // удалить их
        $response = $this->delete(
            '/api/fb-accounts/delete-bulk',
            ['ids' => [$accountToDelete1->id, $accountToDelete2->id]],
            $this->headers
        );
        $response->assertStatus(200);

        // снова взять их из БД
        $accountToDelete1 = FbAccount::where('name', 'accountToDelete1')
            ->first();
        $accountToDelete2 = FbAccount::where('name', 'accountToDelete2')
            ->first();


        // проверить факт их удаления
        $this->assertNotEmpty($accountToDelete1->deleted_at);
        $this->assertNotEmpty($accountToDelete2->deleted_at);

        // проверить ответ API
        $response->assertJsonPath('success', true);
    }
}
