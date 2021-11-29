<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProxyPermission
 *
 * @property string $id
 * @property string $proxy_id
 * @property string $team_id
 * @property string $from_user_id
 * @property string $to_user_id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyPermission whereFromUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyPermission whereProxyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyPermission whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyPermission whereToUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyPermission whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProxyPermission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProxyPermission extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'proxy_id',
        'team_id',
        'from_user_id',
        'to_user_id',
        'type'
    ];
}
