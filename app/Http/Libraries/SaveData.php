<?php

namespace App\Http\Libraries;

use App\Models\FbInsights;
use Carbon\Carbon;
use Illuminate\Support\Str;

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

    public function saveDataInsights($data, string $dbObjectId, $level)
    {
        $fill = collect($data)->map(function ($item) use ($dbObjectId, $level) {
            try {
                return collect($item)->only([
                    'impressions',
                    'spend'
                ])->merge([
                    'id' => Str::uuid()->toString(),
                    'team_id' => $this->account->team_id,
                    'user_id' => $this->account->user_id,
                    'ad_object_id' => $dbObjectId,
                    'level' => $level,
                    'date' => $item['date_start'],
                    'created_at' => Carbon::now()
                ]);
            } catch (\Exception) {
                dd($dbObjectId);
            }
        });

        FbInsights::whereAdObjectId($dbObjectId)->delete();
        FbInsights::insert($fill->toArray());
    }

}
