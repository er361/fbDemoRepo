<?php

namespace App\Http\Libraries;

use App\Models\FbAccount;
use App\Models\FbAccountAd;
use App\Models\FbAccountAdset;
use App\Models\FbAccountCampaign;
use App\Models\FbAdAccount;
use Illuminate\Support\Collection;

class FbFetchBase
{
    use ApiDataFetcher, SaveData;

    const BASE_URL = "https://graph.facebook.com/v12.0/";

    public function __construct(FbAccount $account)
    {
        $this->account = $account;
        $this->initFields();
    }

    private function initFields()
    {
        $fbAdAccount = new FbAdAccount();
        $fbAccountCampaign = new FbAccountCampaign();
        $fbAccountAdset = new FbAccountAdset();
        $fbAccountAd = new FbAccountAd();

        $this->setAdAccountFields(collect($fbAdAccount->getFillable())->implode(','));
        $this->setCampaignFields(collect($fbAccountCampaign->getFillable())->implode(','));
        $this->setAdsetFields(collect($fbAccountAdset->getFillable())->implode(','));
        $this->setAdFields(collect($fbAccountAd->getFillable())->implode(','));
    }

    public function process()
    {
        $adAccounts = $this->getAdAccounts();
        $this->saveData($adAccounts, 'adAccount', $this->account);

        $this->account->adAccounts()->each(function (FbAdAccount $adAccount) {
            $apiData = $this->parseApiData($adAccount);
            $this->saveData($apiData->get('campaigns'), 'campaign', $adAccount);

            $adAccount->campaigns()->each(function (FbAccountCampaign $campaign) use ($apiData) {
                $adsetsByCampaign = collect($apiData->get('adsets'))->where('campaign_id', $campaign->campaign_id);
                $this->saveData($adsetsByCampaign, 'adset', $campaign);

                $campaign->adsets()->each(function (FbAccountAdset $adset) use ($apiData) {
                    $adsByAdset = collect($apiData->get('ads'))->where('adset_id', $adset->adset_id);
                    $this->saveData($adsByAdset, 'ad', $adset);
                });
            });
        });
    }

    private function parseApiData(FbAdAccount $adAccount): Collection
    {
        $data = $this->getBatchData($adAccount->api_id);

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

    private function pagingNext($nextUrl, array &$populateArr)
    {
        $nextPage = $this->getNextPage($nextUrl);
        if (\Arr::exists($nextPage['paging'], 'next')) {
            $populateArr = collect($populateArr)->concat($nextPage['data'])->toArray();

            $this->pagingNext($nextPage['paging']['next'], $populateArr);
        } else {
            $populateArr = collect($populateArr)->concat($nextPage['data'])->toArray();
        }
    }
}


