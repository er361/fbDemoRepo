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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FbAccountAdset[] $adsets
 * @property-read int|null $adsets_count
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereBidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereBidStrategy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereBudgetRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereDailyBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereEffectiveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereFbAdAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereLifetimeBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereObjective($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountCampaign whereUserId($value)
 * @mixin \Eloquent
 */
class FbAccountCampaign extends Model
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
        return $this->hasMany(FbAccountAdset::class, 'fb_account_campaign_id');
    }
}
