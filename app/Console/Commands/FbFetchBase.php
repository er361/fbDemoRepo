<?php

namespace App\Console\Commands;

use App\Libraries\Models\FbAdAccountApi;
use App\Libraries\Models\FbAdApi;
use App\Libraries\Models\FbAdsetApi;
use App\Libraries\Models\FbAppsApi;
use App\Libraries\Models\FbCampaignApi;
use App\Libraries\Models\FbCreativeApi;
use App\Libraries\Models\FbInsightsApi;
use App\Libraries\Models\FbPagesApi;
use App\Models\FbAccount;
use App\Models\FbAd;
use App\Models\FbAdset;
use App\Models\FbCampaign;
use App\Models\FbAdAccount;
use Illuminate\Console\Command;

use function Symfony\Component\Translation\t;


class FbFetchBase extends Command
{
    protected $signature = 'fb-fetch:process {accountId}';

    protected FbAccount $account;

    public function handle()
    {
        $account = FbAccount::withoutGlobalScopes()->find($this->argument('accountId'));
        $this->account = $account;

        $this->process();
    }

    public function process()
    {
        $this->processAdEntities();
        $this->processInsights();
        $this->processPages();
        $this->processApps();
        $this->processCreatives();
    }

    protected function processCreatives()
    {
        $this->info('Get creatives');
        $fbCreativeApi = new FbCreativeApi($this->account);
        $progressBar = $this->getOutput()->createProgressBar($this->account->adAccounts()->count());
        $progressBar->start();

        $this->account->adAccounts()->each(function (FbAdAccount $adAccount) use ($fbCreativeApi, $progressBar) {
            $progressBar->advance();
            $this->info(sprintf("\n Get creatives to account : %s", $adAccount->name));

            $creatives = $fbCreativeApi->getCreatives($adAccount);
            $creativesAll = $fbCreativeApi->withPaginate($creatives)['data'];
            $fbCreativeApi->saveData($creativesAll, $adAccount);
        });
    }

    protected function processPages()
    {
        $this->info("\n Get Pages");
        $fbPagesApi = new FbPagesApi($this->account);
        $pagesData = $fbPagesApi->getPagesWithPaginate()['data'];
        $this->info("Save pages");
        $fbPagesApi->saveData($pagesData);
    }

    protected function processApps()
    {
        $this->info("\n Get apps");
        $fbAppsApi = new FbAppsApi($this->account);
        $this->account->adAccounts()->each(function (FbAdAccount $adAccount) use ($fbAppsApi) {
            $apps = $fbAppsApi->getAppsWithPaginate($adAccount)['data'];
            $this->info('Save apps');
            $fbAppsApi->saveData($apps, $adAccount);
        });
    }

    protected function processInsights()
    {
        $fbInsightsApi = new FbInsightsApi($this->account);
        $progressBar = $this->getOutput()->createProgressBar($this->account->adAccounts->count());
        $this->info("\nget insighst");
        $progressBar->start();
        $this->account->adAccounts()->each(function (FbAdAccount $adAccount) use ($fbInsightsApi, $progressBar) {
            $this->info(sprintf("\nget insight to ad account : %s", $adAccount->name));

            $adAccountInsghts = $fbInsightsApi->getInsightsWithPaginate($adAccount->ad_account_id, 'adAccount');
            $fbInsightsApi->saveData($adAccountInsghts, $adAccount->ad_account_id, 'adAccount');

            $this->info("\n get insights to Campaigns \n");

            $campaignProgressBar = $this->getOutput()->createProgressBar($adAccount->campaigns()->count());
            $campaignProgressBar->start();

            $adAccount->campaigns()->each(function (FbCampaign $campaign) use ($fbInsightsApi, $campaignProgressBar) {
                $this->info("\nget insights to campaign : " . $campaign->name);
                $campaignInsgths = $fbInsightsApi->getInsightsWithPaginate($campaign->campaign_id, 'campaign');
                $this->info('save');
                $fbInsightsApi->saveData($campaignInsgths, $campaign->campaign_id, 'campaign');
                $campaignProgressBar->advance();
            });

            $this->info("\n get insights to Adsets \n");

            $adsetsProgerssBar = $this->getOutput()->createProgressBar($adAccount->adsets()->count());
            $adsetsProgerssBar->start();

            $adAccount->adsets()->each(function (FbAdset $adset) use ($fbInsightsApi, $adsetsProgerssBar) {
                $this->info("\nget insights to adset : " . $adset->name);

                $adsetInsghts = $fbInsightsApi->getInsightsWithPaginate($adset->adset_id, 'adset');
                $fbInsightsApi->saveData($adsetInsghts, $adset->adset_id, 'adset');

                $adsetsProgerssBar->advance();
            });

            $this->info("\n get insights to Ads \n");

            $adsProgressBar = $this->getOutput()->createProgressBar($adAccount->ads()->count());
            $adsProgressBar->start();

            $adAccount->ads()->each(function (FbAd $ad) use ($fbInsightsApi, $adsProgressBar) {
                $this->info("\nget insights to ad : " . $ad->name);

                $adInsghts = $fbInsightsApi->getInsightsWithPaginate($ad->ad_id, 'ad');
                $fbInsightsApi->saveData($adInsghts, $ad->ad_id, 'ad');
                $adsProgressBar->advance();
            });

            $progressBar->advance();
        });
    }

    protected function processAdEntities(): void
    {
        $progressBar = $this->getOutput()->createProgressBar($this->account->adAccounts()->count());
        $fbAdAccountApi = new FbAdAccountApi($this->account);
        $fbAdAccountApi->setProgressBar($progressBar);

        $this->info('Запрашиваем Ad accounts из апи фб');
        $progressBar->start();
        $adAccounts = $fbAdAccountApi->getAdAccountsWithPaginate();

        $this->info("\nСохраняем Ad accounts в базу");
        $fbAdAccountApi->saveData($adAccounts);

        $this->info('Для каждого Ad account');
        $this->account->adAccounts()->each(function (FbAdAccount $adAccount) use ($fbAdAccountApi, $progressBar) {
            $this->info('Получаем данные батчем для аккаунта :' . $adAccount->name);
            $adAccountSubEntities = $fbAdAccountApi->getBatchDataWithPaginate($adAccount->ad_account_id);

            $this->info('Сохраняем компании');
            $fbCampaign = new FbCampaignApi();
            $fbCampaign->setProgressBar($progressBar);
            $fbCampaign->saveData($adAccountSubEntities->get('campaigns'), $adAccount);

            $campaignsBar = $this->getOutput()->createProgressBar($adAccount->campaigns()->count());
            $campaignsBar->start();

            $adAccount->campaigns()->each(function (FbCampaign $campaign) use ($adAccountSubEntities, $campaignsBar) {
                $campaignsBar->advance();
                $this->info(sprintf("\nСохраняем адсеты для компании: '%s'", $campaign->name));

                $adsetsByCampaign = collect($adAccountSubEntities->get('adsets'))->where(
                    'campaign_id',
                    $campaign->campaign_id
                );
                $fbCampaignApi = new FbAdsetApi();
                $fbCampaignApi->saveData($adsetsByCampaign->toArray(), $campaign);

                $this->info('Сохраняем ads');
                $adsetsBar = $this->getOutput()->createProgressBar($campaign->adsets()->count());
                $adsetsBar->start();

                $campaign->adsets()->each(function (FbAdset $adset) use ($adAccountSubEntities, $adsetsBar) {
                    $adsetsBar->advance();
                    $this->info(sprintf("\nЗапрашиваю данные по adset : '%s'", $adset->name));
                    $adsByAdset = collect($adAccountSubEntities->get('ads'))->where('adset_id', $adset->adset_id);
                    $this->info('Пишу в базу ads');

                    $fbAdApi = new FbAdApi();
                    $fbAdApi->saveData($adsByAdset->toArray(), $adset);
                });
            });
            $progressBar->advance();
        });
    }
}


