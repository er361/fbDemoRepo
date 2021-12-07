<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FbAdAccount
 *
 * @property string $id
 * @property int $ad_account_id
 * @property string $fb_account_id
 * @property string $team_id
 * @property string $user_id
 * @property string $name
 * @property float $amount_spent
 * @property int $account_status
 * @property float $balance
 * @property string $business_city
 * @property string $business_country_code
 * @property string $business_name
 * @property string $business_street
 * @property string $business_street2
 * @property int $is_notifications_enabled
 * @property string $currency
 * @property int $disable_reason
 * @property array|null $funding_source_details
 * @property int $is_personal
 * @property int $timezone_offset_hours_utc
 * @property int $adtrust_dsl
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FbAccount $account
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FbApp[] $apps
 * @property-read int|null $apps_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FbCampaign[] $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FbInsight[] $insights
 * @property-read int|null $insights_count
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereAccountStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereAdAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereAdtrustDsl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereAmountSpent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereBusinessCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereBusinessCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereBusinessStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereBusinessStreet2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereDisableReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereFbAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereFundingSourceDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereIsNotificationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereIsPersonal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereTimezoneOffsetHoursUtc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAdAccount whereUserId($value)
 * @mixin \Eloquent
 */
class FbAdAccount extends Model
{
    use HasFactory, Uuid;

    const ACCOUNT_STATUS_ACTIVE = 1;
    const ACCOUNT_STATUS_DISABLED = 2;
    const ACCOUNT_STATUS_UNSETTLED = 3;
    const ACCOUNT_STATUS_PENDING_REVIEW = 7;
    const ACCOUNT_STATUS_IN_GRACE_PERIOD = 9;
    const ACCOUNT_STATUS_TEMPORARY_UNAVAILABLE = 101;
    const ACCOUNT_STATUS_PENDING_CLOSURE = 100;

    public $incrementing = false;

    protected $fillable = [
        'ad_account_id',
        'fb_account_id',
        'team_id',
        'user_id',

        'name',
        'amount_spent',
        'account_status',
        'balance',
        'business',
        'business_city',
        'business_country_code',
        'business_name',
        'business_state',
        'business_street',
        'business_street2',
        'business_zip',
        'is_notifications_enabled',
        'currency',
        'disable_reason',
        'funding_source_details',
        'is_personal',
        'timezone_offset_hours_utc',
        'adtrust_dsl'
    ];

    protected $casts = [
        'funding_source_details' => 'array'
    ];


    public function getAdAccountIdAttribute($val)
    {
        return 'act_' . $val;
    }

    public function account()
    {
        return $this->belongsTo(FbAccount::class);
    }

    public function campaigns()
    {
        return $this->hasMany(FbCampaign::class, 'fb_ad_account_id');
    }

    public function adsets()
    {
        return $this->hasMany(FbAdset::class);
    }

    public function ads()
    {
        return $this->hasMany(FbAd::class);
    }

    public function creatives()
    {
        return $this->hasMany(FbCreative::class);
    }

    public function apps()
    {
        return $this->hasMany(FbApp::class);
    }

    public function insights()
    {
        return $this->hasMany(FbInsight::class, 'object_id', 'account_id');
    }


}
