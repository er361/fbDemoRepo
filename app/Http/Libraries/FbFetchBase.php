<?php

namespace App\Http\Libraries;

use App\Models\FbAccount;
use App\Models\FbAccountAd;
use App\Models\FbAccountAdset;
use App\Models\FbAccountCampaign;
use App\Models\FbAdAccount;
use Illuminate\Support\Collection;

use function Symfony\Component\Translation\t;

class FbFetchBase
{
    use ApiDataFetcher, SaveData;

    const BASE_URL = "https://graph.facebook.com/v12.0/";
    private $apiInsigtsSince = '2011-01-01';

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
        $this->processAdEntities();
        $this->processInsights();
        $this->processPages();
        $this->processApps();
    }

    /**
     * @return mixed
     */
    public function processPages()
    {
        $pagesData = $this->getPagesWithPaginate()['data'];
        $this->savePages($pagesData);
    }

    public function processApps()
    {
        $this->account->adAccounts()->each(function (FbAdAccount $adAccount) {
            $apps = $this->getAppsWithPaginate($adAccount->api_id)['data'];
            $this->saveApps($apps, $adAccount);
        });
    }

    private function processInsights()
    {
        $accountRelations = $this->account->getFlatRelations();

        $accountRelations['adAccounts']->each(function (FbAdAccount $adAccount) {
            $adAccountData = $this->getInsightsWithPaginate($adAccount->api_id, 'account')['data'];
            $this->saveDataInsights($adAccountData, $adAccount->api_id, 'adAccount');
        });

        $accountRelations['campaigns']->each(function (FbAccountCampaign $campaign) {
            $campaignData = $this->getInsightsWithPaginate($campaign->campaign_id, 'campaign')['data'];
            $this->saveDataInsights($campaignData, $campaign->campaign_id, 'campaign');
        });

        $accountRelations['adsets']->each(function (FbAccountAdset $adset) {
            $adsetData = $this->getInsightsWithPaginate($adset->adset_id, 'adset')['data'];
            $this->saveDataInsights($adsetData, $adset->adset_id, 'adset');
        });

        $accountRelations['ads']->each(function (FbAccountAd $ad) {
            $adData = $this->getInsightsWithPaginate($ad->ad_id, 'ad')['data'];
            $this->saveDataInsights($adData, $ad->ad_id, 'ad');
        });
    }

    private function processAdEntities(): void
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

    /**
     * @param FbAdAccount $adAccount
     * @return array|mixed
     */
    public function getInsightsWithPaginate($adObjectId, $level): mixed
    {
        $insights = $this->getInsights($adObjectId, $level);
        if (\Arr::exists($insights['paging'], 'next')) {
            $this->pagingNext($insights['paging']['next'], $insights);
        }
        return $insights;
    }

    public function getAppsWithPaginate($adAccountId)
    {
        $apps = $this->getApps($adAccountId);

        if (\Arr::exists($apps['paging'], 'next')) {
            $this->pagingNext($apps['paging']['next'], $apps);
        }
        return $apps;
    }

    public function getPagesWithPaginate(): mixed
    {
        $pages = $this->getPages();
        if (\Arr::exists($pages['paging'], 'next')) {
            $this->pagingNext($pages['paging']['next'], $pages);
        }
        return $pages;
    }
}


