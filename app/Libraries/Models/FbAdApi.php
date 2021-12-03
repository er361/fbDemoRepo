<?php

namespace App\Libraries\Models;

use App\Models\FbAdset;

class FbAdApi
{
    public function saveData(array $data, FbAdset $adset)
    {
        $fill = collect($data)->map(function ($item) use ($adset) {
            return array_merge($item, [
                'team_id' => $adset->team_id,
                'user_id' => $adset->user_id,
                'ad_id' => $item['id'],
                'fb_ad_account_id' => $adset->fb_ad_account_id,
                'fb_campaign_id' => $adset->fb_campaign_id
            ]);
        });

        $adset->ads()->delete();
        $adset->ads()->createMany($fill);
    }
}
