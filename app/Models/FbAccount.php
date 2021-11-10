<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use App\Models\Scopes\TeamScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class FbAccount extends Model
{
    use HasFactory, SoftDeletes;

    use Uuid;

    const PERMISSION_TYPE_VIEW = 'view';
    const PERMISSION_TYPE_STAT = 'stat';
    const PERMISSION_TYPE_ACTIONS = 'actions';
    const PERMISSION_TYPE_SHARE = 'share';

    protected static function booted()
    {
        static::addGlobalScope(new TeamScope());
    }

    public function scopeByRole(Builder $query)
    {
        switch (Auth::user()->role) {
            case User::ROLE_TEAM_LEAD:
                return $query->whereRelation('user.teamleads', 'teamlead_id', Auth::id());
            case User::ROLE_FARMER:
            case User::ROLE_USER:
                return $query->where('user_id', Auth::id());
        }
    }

    protected $fillable = [
        'user_id',
        'team_id',
        'proxy_id',
        'name',
        'access_token',
        'business_access_token',
        'login',
        'password',
        'user_agent',
        'cookies',
        'archived',
        'facebook_id',
        'status',
        'notes'
    ];

    public function tags()
    {
        return $this->hasMany(FbAccountsTag::class, 'account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proxy()
    {
        return $this->belongsTo(Proxy::class);
    }

    public function permissions()
    {
        return $this->hasMany(FbAccountsPermission::class, 'account_id');
    }
}
