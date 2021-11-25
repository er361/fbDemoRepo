<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAccountCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'account_id',
        'db_fb_account_id',
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
        return $this->hasMany(FbAccountAdset::class, 'campaign_id');
    }
}
