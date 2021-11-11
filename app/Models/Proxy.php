<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use function Symfony\Component\Translation\t;

class Proxy extends BaseModel
{
    use HasFactory, Uuid, SoftDeletes;

    const STATUS_NEW = 'new';
    const STATUS_ACTIVE = 'active';
    const STATUS_ERROR = 'error';

    const PERMISSION_ADMIN = 'admin';

    protected $table = 'proxies';
    protected $fillable = [
        'team_id',
        'user_id',
        'type',
        'name',
        'host',
        'port',
        'login',
        'password',
        'change_ip_url',
        'status',
        'external_ip',
        'change_ip_url',
        'expiration_date',
    ];

    public function scopeActionsByRole(Builder $query)
    {
        if (Auth::user()->role !== User::ROLE_ADMIN) {
            $query->where(function (Builder $builder) {
                $builder->where('user_id', Auth::id())
                    ->orWhereHas('permissions', fn(Builder $q) => $q->where([
                        'to_user_id' => Auth::id(),
                        'type' => self::PERMISSION_ADMIN
                    ]));
            });
        }

        if (Auth::user()->role == User::ROLE_TEAM_LEAD) {
            $query->orWhereRelation('user.teamleads', 'teamlead_id', Auth::id())
                ->orWhere(function (Builder $builder) {
                    $builder->whereHas('permissions', function (Builder $builder) {
                        $builder->whereIn('to_user_id', function ($builder) {
                            $builder->select('user_id')
                                ->from('user_teamlead')
                                ->where('teamlead_id', Auth::id());
                        })->where('type', self::PERMISSION_ADMIN);
                    });
                });
        }

        return $query;
    }

    public function check()
    {
        try {
            $credentials = $this->login && $this->password ? "$this->login:$this->password@" : '';
            $url = "$this->type://$credentials$this->host:$this->port";
            $res = Http::withOptions([
                'timeout' => 10.0,
                "proxy" => $url
            ])->get('http://ip-api.com/json');

            $this->update([
                'status' => self::STATUS_ACTIVE,
                'external_ip' => $res->json('query')
            ]);
        } catch (\Exception $exception) {
            $this->update(['status' => self::STATUS_ERROR]);
            Log::warning($exception->getMessage());
            return false;
        }
        return true;
    }

    public function permissions()
    {
        return $this->hasMany(ProxyPermission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
