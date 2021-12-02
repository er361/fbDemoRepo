<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FbAccountAd
 *
 * @property string $id
 * @property int $ad_id
 * @property int $account_id
 * @property int $campaign_id
 * @property int $adset_id
 * @property string $team_id
 * @property string $user_id
 * @property string $fb_account_adset_id
 * @property string $name
 * @property string $status
 * @property string $effective_status
 * @property string|null $daily_budget
 * @property string|null $lifetime_budget
 * @property string|null $budget_remaining
 * @property array|null $ad_review_feedback
 * @property string $creative_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereAdReviewFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereAdsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereBudgetRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereCreativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereDailyBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereEffectiveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereFbAccountAdsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereLifetimeBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountAd whereUserId($value)
 * @mixin \Eloquent
 */
class FbAccountAd extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'name',
        'ad_id',
        'account_id',
        'campaign_id',
        'adset_id',

        'fb_adset_id',
        'fb_campaign_id',
        'fb_ad_account_id',

        'team_id',
        'user_id',
        'status',
        'effective_status',
        'daily_budget',
        'lifetime_budget',
        'budget_remaining',
        'ad_review_feedback',
        'creative_id'
    ];

    protected $casts = [
        'ad_review_feedback' => 'json'
    ];


}
