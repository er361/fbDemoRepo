<?php

namespace App\Libraries\Models;

use App\Libraries\FbApiQuery;
use App\Models\FbAccount;
use App\Models\FbAdAccount;

class FbCampaignApi extends FbApiQuery
{
    public function saveData(array $data, FbAdAccount $adAccount)
    {
        $fill = collect($data)->map(function ($item) use ($adAccount) {
            return array_merge($item, [
                'team_id' => $adAccount->team_id,
                'user_id' => $adAccount->user_id,
                'campaign_id' => $item['id']
            ]);
        });
        $adAccount->campaigns()->delete();
        $adAccount->campaigns()->createMany($fill);
    }

    public function getFbAccount(): FbAccount
    {
        // TODO: Implement getFbAccount() method.
    }
}
