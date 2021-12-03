<?php

namespace App\Libraries\Models;

use App\Models\FbAdAccount;
use App\Models\FbCampaign;

class FbAdsetApi
{
    public function saveData(array $data, FbCampaign $campaign)
    {
        $fill = collect($data)->map(function ($item) use ($campaign) {
            return array_merge($item, [
                'team_id' => $campaign->team_id,
                'user_id' => $campaign->user_id,
                'adset_id' => $item['id'],
                'fb_ad_account_id' => $campaign->fb_ad_account_id,
            ]);
        });

        $campaign->adsets()->delete();
        $campaign->adsets()->createMany($fill);
    }
}
