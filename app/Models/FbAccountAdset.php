<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAccountAdset extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'name',
        'adset_id',
        'account_id',
        'campaign_id',
        'fb_account_campaign_id',
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
