<?php

namespace App\Libraries\Models;

use App\Libraries\FbApiQuery;
use App\Models\FbAccount;
use App\Models\FbInsight;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class FbInsightsApi extends FbApiQuery
{
    private FbAccount $account;

    /**
     * @param FbAccount $account
     */
    public function __construct(FbAccount $account)
    {
        $this->account = $account;
    }

    public function getInsightsWithPaginate($adIbjectId, $level)
    {
        $insights = $this->getInsights($adIbjectId, $level);
        return $this->withPaginate($insights)['data'];
    }

    public function getInsights($adObjectId, $level)
    {
        $url = self::BASE_URL . $adObjectId . '/insights';

        $res = $this->client($url, 'GET', [
            'fields' => 'impressions,spend',
            'access_token' => $this->account->access_token,
            'level' => $level == 'adAccount' ? 'account' : $level,
            'time_increment' => 1,
            'time_range' => [
                'since' => '2011-01-01',
                'until' => Carbon::now()->toDateString()
            ]
        ]);


        return $res->json();
    }

    public function saveData($data, string $adObjectId, $level)
    {
        $fill = collect($data)->map(function ($item) use ($adObjectId, $level) {
            return collect($item)->only([
                'impressions',
                'spend'
            ])->merge([
                'id' => Str::uuid()->toString(),
                'team_id' => $this->account->team_id,
                'user_id' => $this->account->user_id,
                'fb_account_id' => $this->account->id,
                'object_id' => $level == 'adAccount' ? explode('_', $adObjectId)[1] : $adObjectId,
                'level' => $level,
                'date' => $item['date_start'],
                'created_at' => \Carbon\Carbon::now()
            ]);
        });

        FbInsight::whereObjectId($adObjectId)->delete();
        FbInsight::insert($fill->toArray());
    }

    public function getFbAccount(): FbAccount
    {
        return $this->account;
    }
}
