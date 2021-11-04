<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAccount extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'user_id',
        'name',
        'access_token',
        'business_access_token',
        'password' => 'string',
        'user_agent',
        'cookies',
    ];

    public function tags()
    {
        return $this->hasMany(FbAccountsTag::class,'user_id','user_id');
    }

    public function permissions()
    {
        return $this->hasMany(FbAccountsPermission::class,'user_id');
    }
}
