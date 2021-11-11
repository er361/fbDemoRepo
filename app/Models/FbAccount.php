<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use App\Models\Scopes\TeamScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\Extension\Footnote\Node\FootnoteBackref;

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

    public function scopeActionsByRole(Builder $query, $permissionType = FbAccount::PERMISSION_TYPE_VIEW)
    {
        if (Auth::user()->role !== User::ROLE_ADMIN) {
            $query->where(function (Builder $builder) use ($permissionType) {
                $builder->where('user_id', Auth::id())
                    ->orWhereHas('permissions', fn(Builder $q) => $q->where([
                        'to_user_id' => Auth::id(),
                        'type' => $permissionType
                    ]));
            });
        }

        if (Auth::user()->role == User::ROLE_TEAM_LEAD) {
            $query->orWhereRelation('user.teamleads', 'teamlead_id', Auth::id())
                ->orWhere(function (Builder $builder) use ($permissionType) {
                    $builder->whereHas('permissions', function (Builder $builder) use ($permissionType) {
                        $builder->whereIn('to_user_id', function ($builder) {
                            $builder->select('user_id')
                                ->from('user_teamlead')
                                ->where('teamlead_id', Auth::id());
                        })->where('type', $permissionType);
                    });
                });
        }

        return $query;
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
