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
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereAdId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereAdReviewFeedback($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereAdsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereBudgetRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereCreativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereDailyBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereEffectiveStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereFbAccountAdsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereLifetimeBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereUserId($value)
 * @mixin \Eloquent
 * @property string $fb_ad_account_id
 * @property string $fb_campaign_id
 * @property string $fb_adset_id
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereFbAdAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereFbAdsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAd whereFbCampaignId($value)
 */
class FbAd extends Model
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
