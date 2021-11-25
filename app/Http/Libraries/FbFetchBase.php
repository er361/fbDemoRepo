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
        $this->saveData($adAccounts, 'adAccounts');

        collect($adAccounts)->each(function ($adAccount) {
            $campaignFields = collect((new FbAccountCampaign())->getFillable())->implode(',');
            $adsetsFields = collect((new FbAccountAdset())->getFillable())->implode(',');
            $adFields = collect((new FbAccountAd())->getFillable())->implode(',');

            $subQueries = [
                [
                    'method' => 'GET',
                    'relative_url' => $adAccount['id'] . '/campaigns?fields=' . $campaignFields
                ],
                [
                    'method' => 'GET',
                    'relative_url' => $adAccount['id'] . '/adsets?fields=' . $adsetsFields
                ],
                [
                    'method' => 'GET',
                    'relative_url' => $adAccount['id'] . '/ads?fields=' . $adFields
                ]
            ];

            $response = \Http::post(self::BASE_URL, [
                'batch' => $subQueries,
                'access_token' => $this->account->access_token
            ]);

            $campaigns = json_decode($response->json()[0]['body'], true)['data'];
            $adsets = json_decode($response->json()[1]['body'], true)['data'];
            $ads = json_decode($response->json()[2]['body'], true)['data'];

//            dump($campaigns,$adsets,$ads);
            $this->saveData($campaigns, 'campaigns');
            $this->saveData($adsets, 'adsets');
            $this->saveData($ads, 'ads');
        });
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
     * @param mixed $data
     */
    public function saveData(mixed $data, $type): void
    {
        $fill = collect($data)->map(function ($item) {
            return array_merge($item, [
                'team_id' => $this->account->team_id,
                'user_id' => $this->account->user_id
            ]);
        });

        switch ($type) {
            case 'adAccounts':
                $this->account->adAccounts()->delete();
                $this->account->adAccounts()->createMany($fill);
                break;
            case 'campaigns':
                $this->account->campaigns()->delete();
                $this->account->campaigns()->createMany($fill);
                break;
            case 'adsets':
                $this->account->adsets()->delete();
                $this->account->adsets()->createMany($fill);
                break;
            case 'ads':
                $this->account->ads()->delete();
                $this->account->ads()->createMany($fill);
                break;
        }
    }
}


