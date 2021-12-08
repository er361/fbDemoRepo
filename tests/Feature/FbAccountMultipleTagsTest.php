<?php

namespace Tests\Feature;

use App\Models\FbAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FbAccountMultipleTagsTest extends TestCase
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

    public function test_add_tags()
    {
        $a1 = FbAccount::where('name', 'accountForMultipleTags_1')->first();
        $a2 = FbAccount::where('name', 'accountForMultipleTags_2')->first();

        $tags = ['tag1', 'tag2'];

        $response = $this->put('/');

        $response->assertStatus(200);
    }
}
