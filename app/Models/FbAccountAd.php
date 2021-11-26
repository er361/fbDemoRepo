<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAccountAd extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'name',
        'ad_id',
        'account_id',
        'campaign_id',
        'adset_id',
        'fb_account_adset_id',
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
