<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FbAccountsPermission
 *
 * @property string $id
 * @property string $account_id
 * @property string $from_user_id
 * @property string $to_user_id
 * @property string $team_id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsPermission whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsPermission whereFromUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsPermission whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsPermission whereToUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsPermission whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsPermission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FbAccountsPermission extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'account_id',
        'from_user_id',
        'to_user_id',
        'team_id',
        'type'
    ];
}
