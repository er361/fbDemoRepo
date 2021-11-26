<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
