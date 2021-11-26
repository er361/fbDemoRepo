<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'user_id',
        'account_id',
        'api_id',
        'team_id',
        'name',
        'fb_account_id',
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

    public function campaigns()
    {
        return $this->hasMany(FbAccountCampaign::class, 'fb_ad_account_id');
    }


}
