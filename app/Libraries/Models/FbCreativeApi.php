<?php

namespace App\Libraries\Models;

use App\Libraries\FbApiQuery;
use App\Models\FbAccount;
use App\Models\FbAdAccount;
use App\Models\FbCreative;

class FbCreativeApi extends FbApiQuery
{
    protected FbAccount $account;

    /**
     * @param FbAccount $account
     */
    public function __construct(FbAccount $account)
    {
        $this->account = $account;
    }

    public function getCreatives(FbAdAccount $adAccount)
    {
        $res = $this->client(
            $this->getBaseUrl() . $adAccount->ad_account_id . '/adcreatives',
            'GET',
            [
                'fields' => 'account_id,effective_instagram_story_id,effective_object_story_id,instagram_permalink_url,object_story_spec',
            ]
        );
        return $res->json();
    }

    public function saveData($data, FbAdAccount $adAccount)
    {
        $fill = collect($data)->map(function ($item) use ($adAccount) {
            return array_merge($item, [
                'team_id' => $adAccount->team_id,
                'user_id' => $adAccount->user_id,
                'creative_id' => $item['id'],
            ]);
        });

        $adAccount->creatives()->delete();
        $adAccount->creatives()->createMany($fill->toArray());
    }

    public function getFbAccount(): FbAccount
    {
        return $this->account;
    }
}
