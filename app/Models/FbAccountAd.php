<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAccountAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'account_id',
        'db_fb_account_id',
        'campaign_id',
        'adset_id',
        'team_id',
        'user_id'
    ];
}
