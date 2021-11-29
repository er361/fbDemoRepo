<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Proxy
 *
 * @property string $id
 * @property string $team_id
 * @property string $user_id
 * @property string $type
 * @property string|null $name
 * @property string $host
 * @property int $port
 * @property string|null $login
 * @property string|null $password
 * @property string|null $change_ip_url
 * @property string $status
 * @property string|null $external_ip
 * @property string|null $expiration_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProxyPermission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\User $user
 * @method static Builder|Proxy actionsByRole()
 * @method static \Database\Factories\ProxyFactory factory(...$parameters)
 * @method static Builder|Proxy newModelQuery()
 * @method static Builder|Proxy newQuery()
 * @method static \Illuminate\Database\Query\Builder|Proxy onlyTrashed()
 * @method static Builder|Proxy query()
 * @method static Builder|Proxy whereChangeIpUrl($value)
 * @method static Builder|Proxy whereCreatedAt($value)
 * @method static Builder|Proxy whereDeletedAt($value)
 * @method static Builder|Proxy whereExpirationDate($value)
 * @method static Builder|Proxy whereExternalIp($value)
 * @method static Builder|Proxy whereHost($value)
 * @method static Builder|Proxy whereId($value)
 * @method static Builder|Proxy whereLogin($value)
 * @method static Builder|Proxy whereName($value)
 * @method static Builder|Proxy wherePassword($value)
 * @method static Builder|Proxy wherePort($value)
 * @method static Builder|Proxy whereStatus($value)
 * @method static Builder|Proxy whereTeamId($value)
 * @method static Builder|Proxy whereType($value)
 * @method static Builder|Proxy whereUpdatedAt($value)
 * @method static Builder|Proxy whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Proxy withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Proxy withoutTrashed()
 * @mixin \Eloquent
 */
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
