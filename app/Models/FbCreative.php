<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbCreative extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'creative_id',
        'user_id',
        'team_id',
        'fb_account_id',
        'fb_ad_account_id',

        'effective_instagram_story_id',
        'effective_object_story_id',
        'instagram_permalink_url',
        'object_story_spec',
        'fb_data'
    ];

    protected $casts = [
        'object_story_spec' => 'json'
    ];
}
