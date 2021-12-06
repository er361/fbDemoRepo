<?php

namespace App\Libraries;

use App\Libraries\Models\FbAdAccountApi;
use App\Libraries\Models\FbAdApi;
use App\Libraries\Models\FbAdsetApi;
use App\Libraries\Models\FbAppsApi;
use App\Libraries\Models\FbCampaignApi;
use App\Libraries\Models\FbInsightsApi;
use App\Libraries\Models\FbPagesApi;
use App\Models\FbAccount;
use App\Models\FbAd;
use App\Models\FbAdset;
use App\Models\FbCampaign;
use App\Models\FbAdAccount;
use App\Models\FbInsight;
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
    }

    public function process()
    {
//        $this->processAdEntities();
//        $this->processInsights();
        $this->processPages();
//        $this->processApps();
    }

    public function processPages()
    {
        $fbPagesApi = new FbPagesApi($this->account);
        $pagesData = $fbPagesApi->getPagesWithPaginate()['data'];
        $fbPagesApi->saveData($pagesData);
    }

    public function processApps()
    {
        $fbAppsApi = new FbAppsApi($this->account);
        $this->account->adAccounts()->each(function (FbAdAccount $adAccount) use ($fbAppsApi) {
            $apps = $fbAppsApi->getAppsWithPaginate($adAccount)['data'];
            $fbAppsApi->saveData($apps, $adAccount);
        });
    }

    private function processInsights()
    {
        $fbInsightsApi = new FbInsightsApi($this->account);

        $this->account->adAccounts()->each(function (FbAdAccount $adAccount) use ($fbInsightsApi) {
            $adAccountInsghts = $fbInsightsApi->getInsightsWithPaginate($adAccount->ad_account_id, 'adAccount');
            $fbInsightsApi->saveData($adAccountInsghts, $adAccount->ad_account_id, 'adAccount');

            $adAccount->campaigns()->each(function (FbCampaign $campaign) use ($fbInsightsApi) {
                $campaignInsgths = $fbInsightsApi->getInsightsWithPaginate($campaign->campaign_id, 'campaign');
                $fbInsightsApi->saveData($campaignInsgths, $campaign->campaign_id, 'campaign');
            });

            $adAccount->adsets()->each(function (FbAdset $adset) use ($fbInsightsApi) {
                $adsetInsghts = $fbInsightsApi->getInsightsWithPaginate($adset->adset_id, 'adset');
                $fbInsightsApi->saveData($adsetInsghts, $adset->adset_id, 'adset');
            });

            $adAccount->ads()->each(function (FbAd $ad) use ($fbInsightsApi) {
                $adInsghts = $fbInsightsApi->getInsightsWithPaginate($ad->ad_id, 'ad');
                $fbInsightsApi->saveData($adInsghts, $ad->ad_id, 'ad');
            });
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
}


