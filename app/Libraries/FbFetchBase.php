<?php

namespace App\Libraries;

use App\Libraries\Models\FbAdAccountApi;
use App\Libraries\Models\FbAdApi;
use App\Libraries\Models\FbAdsetApi;
use App\Libraries\Models\FbCampaignApi;
use App\Models\FbAccount;
use App\Models\FbAd;
use App\Models\FbAdset;
use App\Models\FbCampaign;
use App\Models\FbAdAccount;
use Illuminate\Support\Collection;

use function Symfony\Component\Translation\t;

class FbFetchBase extends FbApiQuery
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
        $fbAccountCampaign = new FbCampaign();
        $fbAccountAdset = new FbAdset();
        $fbAccountAd = new FbAd();

        $this->setCampaignFields(collect($fbAccountCampaign->getFillable())->implode(','));
        $this->setAdsetFields(collect($fbAccountAdset->getFillable())->implode(','));
        $this->setAdFields(collect($fbAccountAd->getFillable())->implode(','));
    }

    public function process()
    {
        $this->processAdEntities();
//        $this->processInsights();
//        $this->processPages();
//        $this->processApps();
    }


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
            $this->saveDataInsights($adAccountData, $adAccount->account_id, 'adAccount');
        });

        $accountRelations['campaigns']->each(function (FbCampaign $campaign) {
            $campaignData = $this->getInsightsWithPaginate($campaign->campaign_id, 'campaign')['data'];
            $this->saveDataInsights($campaignData, $campaign->campaign_id, 'campaign');
        });

        $accountRelations['adsets']->each(function (FbAdset $adset) {
            $adsetData = $this->getInsightsWithPaginate($adset->adset_id, 'adset')['data'];
            $this->saveDataInsights($adsetData, $adset->adset_id, 'adset');
        });

        $accountRelations['ads']->each(function (FbAd $ad) {
            $adData = $this->getInsightsWithPaginate($ad->ad_id, 'ad')['data'];
            $this->saveDataInsights($adData, $ad->ad_id, 'ad');
        });
    }

    private function processAdEntities(): void
    {
        $fbAdAccountApi = new FbAdAccountApi($this->account);
        $adAccounts = $fbAdAccountApi->getAdAccountsWithPaginate();
        $fbAdAccountApi->saveData($adAccounts);


        $this->account->adAccounts()->each(function (FbAdAccount $adAccount) use ($fbAdAccountApi) {
            $adAccountSubEntities = $fbAdAccountApi->getBatchDataWithPaginate($adAccount->ad_account_id);

            $fbCampaign = new FbCampaignApi();
            $fbCampaign->saveData($adAccountSubEntities->get('campaigns'), $adAccount);

            $adAccount->campaigns()->each(function (FbCampaign $campaign) use ($adAccountSubEntities) {
                $adsetsByCampaign = collect($adAccountSubEntities->get('adsets'))->where(
                    'campaign_id',
                    $campaign->campaign_id
                );
                $fbCampaignApi = new FbAdsetApi();
                $fbCampaignApi->saveData($adsetsByCampaign->toArray(), $campaign);


                $campaign->adsets()->each(function (FbAdset $adset) use ($adAccountSubEntities) {
                    $adsByAdset = collect($adAccountSubEntities->get('ads'))->where('adset_id', $adset->adset_id);
                    $fbAdApi = new FbAdApi();
                    $fbAdApi->saveData($adsByAdset->toArray(), $adset);
                });
            });
        });
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


