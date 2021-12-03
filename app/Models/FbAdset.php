<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FbAccountAdset
 *
 * @property string $id
 * @property int $adset_id
 * @property int $account_id
 * @property int $campaign_id
 * @property string $fb_account_campaign_id
 * @property string $team_id
 * @property string $user_id
 * @property string $name
 * @property string $status
 * @property string $effective_status
 * @property string $daily_budget
 * @property string $lifetime_budget
 * @property string $budget_remaining
 * @property string|null $bid_strategy
 * @property string|null $bid_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FbAd[] $ads
 * @property-read int|null $ads_count
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereAdsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereBidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereBidStrategy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereBudgetRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereDailyBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereEffectiveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereFbAccountCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereLifetimeBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereUserId($value)
 * @mixin \Eloquent
 * @property string $fb_campaign_id
 * @property string $fb_ad_account_id
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereFbAdAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdset whereFbCampaignId($value)
 */
class FbAdset extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'name',
        'adset_id',
        'account_id',
        'campaign_id',
        'fb_campaign_id',
        'fb_ad_account_id',
        'team_id',
        'user_id',
        'status',
        'effective_status',
        'daily_budget',
        'lifetime_budget',
        'budget_remaining',
        'bid_strategy',
        'bid_amount'
    ];

    public function ads()
    {
        return $this->hasMany(FbAd::class, 'fb_adset_id');
    }
}
