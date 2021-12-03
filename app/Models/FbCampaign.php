<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FbAccountCampaign
 *
 * @property string $id
 * @property int $campaign_id
 * @property int $account_id
 * @property string $fb_ad_account_id
 * @property string $team_id
 * @property string $user_id
 * @property string $name
 * @property string $status
 * @property string $effective_status
 * @property string|null $daily_budget
 * @property string|null $lifetime_budget
 * @property string $budget_remaining
 * @property string $objective
 * @property string|null $bid_strategy
 * @property string|null $bid_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FbAdset[] $adsets
 * @property-read int|null $adsets_count
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereBidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereBidStrategy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereBudgetRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereDailyBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereEffectiveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereFbAdAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereLifetimeBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereObjective($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbCampaign whereUserId($value)
 * @mixin \Eloquent
 */
class FbCampaign extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'name',
        'campaign_id',
        'account_id',
        'fb_ad_account_id',
        'team_id',
        'user_id',
        'status',
        'effective_status',
        'daily_budget',
        'lifetime_budget',
        'budget_remaining',
        'objective',
        'bid_strategy',
        'bid_amount'
    ];

    public function adsets()
    {
        return $this->hasMany(FbAdset::class, 'fb_campaign_id');
    }
}
