<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAccountPage extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'fb_account_id',
        'page_id',
        'access_token',
        'is_published',
        'picture',
        'name',
        'category',
        'category_list',
        'tasks',
        'cover',
        'team_id',
        'user_id',
    ];

    protected $casts = [
        'picture' => 'json',
        'category_list' => 'json',
        'tasks' => 'json',
        'cover' => 'json'
    ];
}
