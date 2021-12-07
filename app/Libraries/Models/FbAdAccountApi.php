<?php

namespace App\Libraries\Models;

use App\Libraries\FbApiQuery;
use App\Models\FbAccount;
use App\Models\FbAd;
use App\Models\FbAdAccount;
use App\Models\FbAdset;
use App\Models\FbCampaign;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FbAdAccountApi extends FbApiQuery
{
    private FbAccount $account;
    private $fields;

    /**
     * @param FbAccount $account
     */
    public function __construct(FbAccount $account)
    {
        $this->account = $account;
        $this->initFields();
    }

    private function initFields()
    {
        $fields = [
            'name',
            'account_id',
            'amount_spent',
            'account_status',
            'balance',
            'business',
            'business_city',
            'business_country_code',
            'business_name',
            'business_state',
            'business_street',
            'business_street2',
            'business_zip',
            'is_notifications_enabled',
            'currency',
            'disable_reason',
            'funding_source_details',
            'is_personal',
            'timezone_offset_hours_utc',
            'adtrust_dsl'
        ];

        $this->fields = implode(',', $fields);
    }

    public function saveData(array $data)
    {
        $fill = collect($data)->map(function ($item) {
            return array_merge($item, [
                'team_id' => $this->account->team_id,
                'user_id' => $this->account->user_id,
                'ad_account_id' => $item['account_id']
            ]);
        });

        $this->account->adAccounts()->delete();
        $this->account->adAccounts()->createMany($fill);
    }

    public function getAdAccountsWithPaginate()
    {
        $adAccounts = $this->getAdAccounts(false);

        if (\Arr::exists($adAccounts['paging'], 'next')) {
            $this->pagingNext($adAccounts['paging']['next'], $adAccounts);
        }
        return $adAccounts['data'];
    }

    public function getAdAccounts($data = true)
    {
        $res = $this->client(
            $this->getBaseUrl() . $this->account->facebook_id . '/adaccounts',
            'GET',
            [
                'fields' => $this->fields,
                'access_token' => $this->account->access_token
            ]
        );

        return $data ? $res->json()['data'] : $res->json();
    }

    public function getBatchData($adAccountId)
    {
        $fbCampaign = new FbCampaign();
        $campaignFields = implode(',', $fbCampaign->getFillable());

        $fbAdset = new FbAdset();
        $adsetFields = implode(',', $fbAdset->getFillable());

        $fbAd = new FbAd();
        $adFields = implode(',', $fbAd->getFillable());

        $campaigns = [
            'method' => 'GET',
            'relative_url' => $adAccountId . '/campaigns?fields=' . $campaignFields
//            'relative_url' => $adAccountId . '/campaigns?limit=1&fields=' . $this->getCampaignFields()
        ];

        $adsets = [
            'method' => 'GET',
            'relative_url' => $adAccountId . '/adsets?fields=' . $adsetFields
//            'relative_url' => $adAccountId . '/adsets?limit=1&fields=' . $this->getAdsetFields()
        ];

        $ads = [
            'method' => 'GET',
            'relative_url' => $adAccountId . '/ads?fields=' . $adFields
//            'relative_url' => $adAccountId . '/ads?limit=1&fields=' . $this->getAdFields()
        ];

        $subQueries = [
            $campaigns,
            $adsets,
            $ads
        ];

        $res = $this->client($this->getBaseUrl(), 'POST', [
            'batch' => $subQueries,
            'access_token' => $this->account->access_token
        ]);

        return $res->json();
    }

    public function getBatchDataWithPaginate(string $adAccountId): Collection
    {
        $data = $this->getBatchData($adAccountId);

        $campaignsReqData = $data[0];
        $adsetsReqData = $data[1];
        $adsReqData = $data[2];

        $campaignsBody = json_decode($campaignsReqData['body'], true);
        $adsetsBody = json_decode($adsetsReqData['body'], true);
        $adsBody = json_decode($adsReqData['body'], true);

        $campaignsData = $campaignsBody['data'];
        $adsetData = $adsetsBody['data'];
        $adsData = $adsBody['data'];

        if (\Arr::exists($campaignsBody['paging'], 'next')) {
            $this->pagingNext($campaignsBody['paging']['next'], $campaignsData);
        }

        if (\Arr::exists($adsetsBody['paging'], 'next')) {
            $this->pagingNext($adsetsBody['paging']['next'], $adsetData);
        }

        if (\Arr::exists($adsBody['paging'], 'next')) {
            $this->pagingNext($adsBody['paging']['next'], $adsData);
        }

        return collect()
            ->put('campaigns', $campaignsData)
            ->put('adsets', $adsetData)
            ->put('ads', $adsData);
    }

    public function getFbAccount(): FbAccount
    {
        return $this->account;
    }
}
