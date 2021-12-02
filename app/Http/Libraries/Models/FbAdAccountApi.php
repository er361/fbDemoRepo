<?php

namespace App\Http\Libraries\Models;

use App\Http\Libraries\FbApiQuery;
use App\Models\FbAccount;
use App\Models\FbAdAccount;
use Illuminate\Support\Arr;

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
                'query' => [
                    'fields' => $this->fields,
                    'access_token' => $this->account->access_token
                ]
            ]
        );

        return $data ? $res->json()['data'] : $res->json();
    }
}
