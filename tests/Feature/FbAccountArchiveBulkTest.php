<?php

namespace Tests\Feature;

use App\Models\FbAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group accounts
 */
class FbAccountArchiveBulkTest extends TestCase
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

    public function test_successful_archive()
    {
        $accountToArchive = FbAccount::where('name', 'accountToArchive')
            ->select('id')
            ->first();

        $response = $this->put(
            '/api/fb-accounts/archive-bulk',
            ['ids' => [$accountToArchive->id]],
            $this->headers
        );

        $accountToArchive->refresh();

        $response->assertStatus(200);
        $this->assertEquals(1, $accountToArchive->archived, 'archived attribute should be 1');
        $this->assertEquals(1, $accountToArchive->archived, 'archived attribute should be 1');
        $response->assertJsonPath('success', true);
    }
}
