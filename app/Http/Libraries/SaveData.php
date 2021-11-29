<?php

namespace App\Http\Libraries;

trait SaveData
{
    /**
     * @param mixed $data
     */
    private function saveData(mixed $data, $type, $parentModel): void
    {
        $fill = collect($data)->map(function ($item) {
            return array_merge($item, [
                'team_id' => $this->account->team_id,
                'user_id' => $this->account->user_id,
            ]);
        });

        switch ($type) {
            case 'adAccount':
                $adAccounts = $fill->map(fn($item) => array_merge($item, ['api_id' => $item['id']]));
                $parentModel->adAccounts()->delete();
                $parentModel->adAccounts()->createMany($adAccounts);
                break;
            case 'campaign':
                $campaigns = $fill->map(fn($item) => array_merge($item, ['campaign_id' => $item['id']]));
                $parentModel->campaigns()->delete();
                $parentModel->campaigns()->createMany($campaigns);
                break;
            case 'adset':
                $adsets = $fill->map(fn($item) => array_merge($item, ['adset_id' => $item['id']]));
                $parentModel->adsets()->delete();
                $parentModel->adsets()->createMany($adsets);
                break;
            case 'ad':
                $ads = $fill->map(fn($item) => array_merge($item, ['ad_id' => $item['id']]));
                $parentModel->ads()->delete();
                $parentModel->ads()->createMany($ads);
                break;
        }
    }

}
