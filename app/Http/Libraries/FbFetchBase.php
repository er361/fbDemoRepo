<?php

namespace App\Http\Libraries;

use App\Models\FbAccount;
use App\Models\FbAccountAd;
use App\Models\FbAccountAdset;
use App\Models\FbAccountCampaign;
use App\Models\FbAdAccount;

class FbFetchBase
{
    const BASE_URL = "https://graph.facebook.com/v12.0/";
    const DEV_TEAM_ID = '54ca940b-fd36-494c-93c4-77fb75384708';
    const DEV_USER_ID = '54ca940b-fd36-494c-93c4-77fb75384708';

    private FbAccount $account;

    private $campaigns;
    private $adsets;
    private $ads;

    /**
     * @param FbAccount $account
     */
    public function __construct(FbAccount $account)
    {
        $this->account = $account;
    }

    public function fetch()
    {
        $adAccounts = $this->getAdAccounts();
        $this->saveData($adAccounts, 'adAccount', $this->account);

        $this->account->adAccounts()->each(function (FbAdAccount $adAccount) {
            $res = $this->getBatchData($adAccount->api_id);

            $campaigns = json_decode($res->json()[0]['body'], true)['data'];
            $adsets = json_decode($res->json()[1]['body'], true)['data'];
            $ads = json_decode($res->json()[2]['body'], true)['data'];

//            dump($campaigns,$adsets,$ads);
            $this->saveData($campaigns, 'campaign', $adAccount);

            $adAccount->campaigns()->each(function (FbAccountCampaign $campaign) use ($ads, $adsets) {
                $adsetsByCampaign = collect($adsets)->where('campaign_id', $campaign->campaign_id);
                $this->saveData($adsetsByCampaign, 'adset', $campaign);

                $campaign->adsets()->each(function (FbAccountAdset $adset) use ($ads) {
                    $adsByAdset = collect($ads)->where('adset_id', $adset->adset_id);
                    $this->saveData($adsByAdset, 'ad', $adset);
                });
            });
        });
    }

    /**
     * @param mixed $data
     */
    public function saveData(mixed $data, $type, $parentModel): void
    {
        $fill = collect($data)->map(function ($item) {
            return array_merge($item, [
                'team_id' => $this->account->team_id,
                'user_id' => $this->account->user_id,
            ]);
        });

        switch ($type) {
            case 'adAccount':
                $adAccounts = $fill->map(fn($item) => array_merge($item, ['api_id' => $item['id']]));
                $parentModel->adAccounts()->delete();
                $parentModel->adAccounts()->createMany($adAccounts);
                break;
            case 'campaign':
                $campaigns = $fill->map(fn($item) => array_merge($item, ['campaign_id' => $item['id']]));
                $parentModel->campaigns()->delete();
                $parentModel->campaigns()->createMany($campaigns);
                break;
            case 'adset':
                $adsets = $fill->map(fn($item) => array_merge($item, ['adset_id' => $item['id']]));
                $parentModel->adsets()->delete();
                $parentModel->adsets()->createMany($adsets);
                break;
            case 'ad':
                $ads = $fill->map(fn($item) => array_merge($item, ['ad_id' => $item['id']]));
                $parentModel->ads()->delete();
                $parentModel->ads()->createMany($ads);
                break;
        }
    }

    /**
     * @return \Illuminate\Http\Client\Response
     */
    public function getAdAccounts()
    {
        $fbAdAccount = new FbAdAccount();
        $fields = collect($fbAdAccount->getFillable())->implode(',');

        $res = \Http::get(self::BASE_URL . $this->account->facebook_id . '/adaccounts', [
            'fields' => $fields,
            'access_token' => $this->account->access_token
        ]);

        if (\Arr::exists($res->json(), 'error')) {
            throw new \Exception($res->json()['error']['message']);
        }

        return $res->json()['data'];
    }

    /**
     * @param FbAdAccount $adAccount
     * @return \Illuminate\Http\Client\Response
     * @throws \Exception
     */
    public function getBatchData($adAccountId): \Illuminate\Http\Client\Response
    {
        $campaignFields = collect((new FbAccountCampaign())->getFillable())->implode(',');
        $adsetsFields = collect((new FbAccountAdset())->getFillable())->implode(',');
        $adFields = collect((new FbAccountAd())->getFillable())->implode(',');

        $subQueries = [
            [
                'method' => 'GET',
                'relative_url' => $adAccountId . '/campaigns?fields=' . $campaignFields
            ],
            [
                'method' => 'GET',
                'relative_url' => $adAccountId . '/adsets?fields=' . $adsetsFields
            ],
            [
                'method' => 'GET',
                'relative_url' => $adAccountId . '/ads?fields=' . $adFields
            ]
        ];

        $res = \Http::post(self::BASE_URL, [
            'batch' => $subQueries,
            'access_token' => $this->account->access_token
        ]);

        if (\Arr::exists($res->json(), 'error')) {
            throw new \Exception($res->json()['error']['message']);
        }
        return $res;
    }
}


