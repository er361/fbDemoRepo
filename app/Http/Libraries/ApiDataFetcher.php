<?php

namespace App\Http\Libraries;

use App\Models\FbAccount;
use App\Models\FbAccountAd;
use App\Models\FbAccountAdset;
use App\Models\FbAccountCampaign;
use App\Models\FbAdAccount;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

trait ApiDataFetcher
{

    private FbAccount $account;

    private $adAccounts;
    private $campaigns;
    private $adsets;
    private $ads;


    private $adAccountFields;
    private $campaignFields;
    private $adsetFields;
    private $adFields;

    private function getNextPage($url)
    {
        $res = \Http::get($url);

        if (Arr::exists($res->json(), 'error')) {
            $this->handleError($res);
        }

        return $res->json();
    }

    /**
     * @param Response $res
     * @throws \Exception
     */
    protected function handleError(Response $res): void
    {
        throw new \Exception($res->json()['error']['message']);
    }

    private function getAdAccounts()
    {
        $res = \Http::get(self::BASE_URL . $this->account->facebook_id . '/adaccounts', [
            'fields' => $this->getAdAccountFields(),
            'access_token' => $this->account->access_token
        ]);

        if (\Arr::exists($res->json(), 'error')) {
            throw new \Exception($res->json()['error']['message']);
        }

        return $res->json()['data'];
    }

    /**
     * @return mixed
     */
    private function getAdAccountFields()
    {
        return $this->adAccountFields;
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
