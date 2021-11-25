<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAccountAdset extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'account_id',
        'db_fb_account_id',
        'campaign_id',
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
        return $this->hasMany(FbAccountAd::class, 'adset_id');
    }
}
