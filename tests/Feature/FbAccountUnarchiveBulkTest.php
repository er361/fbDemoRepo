<?php

namespace Tests\Feature;

use App\Models\FbAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @group accounts
 */
class FbAccountUnarchiveBulkTest extends TestCase
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

    public function test_successful_unarchive()
    {
        $accountToUnarchive = FbAccount::where('name', 'accountToUnarchive')
            ->select('id')
            ->first();

        $response = $this->put(
            '/api/fb-accounts/unarchive-bulk',
            ['ids' => [$accountToUnarchive->id]],
            $this->headers
        );

        $accountToUnarchive = FbAccount::where('name', 'accountToUnarchive')
            ->first();

        $response->assertStatus(200);
        $this->assertEquals(0, $accountToUnarchive->archived, 'archived attribute should be 0');
        $response->assertJsonPath('success', true);
    }
}
