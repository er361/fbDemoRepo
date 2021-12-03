<?php

namespace App\Libraries;

use App\Models\FbAccount;
use App\Models\FbAd;
use App\Models\FbAdset;
use App\Models\FbCampaign;
use App\Models\FbAdAccount;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

trait ApiDataFetcher
{

    private FbAccount $account;


    private $campaignFields;
    private $adsetFields;
    private $adFields;


    private function getInsights($adObjectId, $level)
    {
        $url = self::BASE_URL . $adObjectId . '/insights';

        $res = \Http::get($url, [
            'fields' => 'impressions,spend',
            'access_token' => $this->account->access_token,
            'level' => $level,
            'time_increment' => 1,
            'time_range' => [
                'since' => $this->apiInsigtsSince,
                'until' => Carbon::now()->toDateString()
            ]
        ]);

        if (Arr::exists($res->json(), 'error')) {
            $this->handleError($res);
        }

        return $res->json();
    }

    private function getPages()
    {
        $res = \Http::get(self::BASE_URL . $this->account->facebook_id . '/accounts', [
            'fields' => 'access_token,is_published,picture,cover,name,tasks,category,category_list',
            'access_token' => $this->account->access_token
        ]);

        if (\Arr::exists($res->json(), 'error')) {
            throw new \Exception($res->json()['error']['message']);
        }

        return $res->json();
    }

    private function getApps($adAccount_id)
    {
        $res = \Http::get(self::BASE_URL . $adAccount_id . '/applications', [
            'fields' => 'id,name,logo_url,supported_platforms,object_store_urls',
            'access_token' => $this->account->access_token
        ]);

        if (\Arr::exists($res->json(), 'error')) {
            throw new \Exception($res->json()['error']['message']);
        }

        return $res->json();
    }

    /**
     * @param string $adAccountId
     * @return array
     * @throws \Exception
     */
    private function getBatchData(string $adAccountId): array
    {
        $campaigns = [
            'method' => 'GET',
            'relative_url' => $adAccountId . '/campaigns?fields=' . $this->getCampaignFields()
//            'relative_url' => $adAccountId . '/campaigns?limit=1&fields=' . $this->getCampaignFields()
        ];

        $adsets = [
            'method' => 'GET',
            'relative_url' => $adAccountId . '/adsets?fields=' . $this->getAdsetFields()
//            'relative_url' => $adAccountId . '/adsets?limit=1&fields=' . $this->getAdsetFields()
        ];

        $ads = [
            'method' => 'GET',
            'relative_url' => $adAccountId . '/ads?fields=' . $this->getAdFields()
//            'relative_url' => $adAccountId . '/ads?limit=1&fields=' . $this->getAdFields()
        ];

        $subQueries = [
            $campaigns,
            $adsets,
            $ads
        ];


        $res = \Http::post(self::BASE_URL, [
            'batch' => $subQueries,
            'access_token' => $this->account->access_token
        ]);


        if (\Arr::exists($res->json(), 'error')) {
            $this->handleError($res);
        }
        return $res->json();
    }


    /**
     * @return mixed
     */
    private function getAdAccountFields()
    {
        return $this->adAccountFields;
    }

    /**
     * @return mixed
     */
    private function getCampaignFields()
    {
        return $this->campaignFields;
    }

    /**
     * @return mixed
     */
    private function getAdsetFields()
    {
        return $this->adsetFields;
    }

    /**
     * @return mixed
     */
    private function getAdFields()
    {
        return $this->adFields;
    }

    /**
     * @param mixed $campaignFields
     */
    private function setCampaignFields($campaignFields): void
    {
        $this->campaignFields = $campaignFields;
    }

    /**
     * @param mixed $adsetFields
     */
    private function setAdsetFields($adsetFields): void
    {
        $this->adsetFields = $adsetFields;
    }

    /**
     * @param mixed $adFields
     */
    private function setAdFields($adFields): void
    {
        $this->adFields = $adFields;
    }

    /**
     * @param mixed $adAccountFields
     */
    private function setAdAccountFields($adAccountFields): void
    {
        $this->adAccountFields = $adAccountFields;
    }

}
