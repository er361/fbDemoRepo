<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FbAccount extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    protected $fillable = [
        'user_id',
        'team_id',
        'name',
        'access_token',
        'business_access_token',
        'password',
        'user_agent',
        'cookies',
    ];

    public function tags()
    {
        return $this->hasMany(FbAccountsTag::class,'account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permissions()
    {
        return $this->hasMany(FbAccountsPermission::class,'account_id');
    }
}
