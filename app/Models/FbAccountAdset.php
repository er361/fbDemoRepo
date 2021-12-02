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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FbAccountAd[] $ads
 * @property-read int|null $ads_count
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereAdsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereBidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereBidStrategy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereBudgetRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereDailyBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereEffectiveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereFbAccountCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereLifetimeBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAdset whereUserId($value)
 * @mixin \Eloquent
 */
class FbAccountAdset extends Model
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
        return $this->hasMany(FbAccountAd::class, 'fb_account_adset_id');
    }
}
