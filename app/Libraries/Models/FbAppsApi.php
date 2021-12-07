<?php

namespace App\Libraries\Models;

use App\Libraries\FbApiQuery;
use App\Models\FbAccount;
use App\Models\FbAdAccount;

class FbAppsApi extends FbApiQuery
{
    private FbAccount $account;

    /**
     * @param FbAccount $account
     */
    public function __construct(FbAccount $account)
    {
        $this->account = $account;
    }

    public function getAppsWithPaginate($adAccountId)
    {
        $apps = $this->getApps($adAccountId);

        if (\Arr::exists($apps['paging'], 'next')) {
            $this->pagingNext($apps['paging']['next'], $apps);
        }
        return $apps;
    }

    public function getApps(FbAdAccount $adAccount)
    {
        $res = $this->client($this->getBaseUrl() . $adAccount->ad_account_id . '/applications', 'GET', [
            'fields' => 'id,name,logo_url,supported_platforms,object_store_urls',
            'access_token' => $this->account->access_token
        ]);

        return $res->json();
    }

    public function saveData(array $data, FbAdAccount $adAccount)
    {
        $fill = collect($data)->map(function ($item) use ($adAccount) {
            return array_merge($item, [
                'team_id' => $adAccount->team_id,
                'user_id' => $adAccount->user_id,
                'app_id' => $item['id']
            ]);
        });

        $adAccount->apps()->delete();
        $adAccount->apps()->createMany($fill);
    }

    public function getFbAccount(): FbAccount
    {
        return $this->account;
    }
}
