<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FbAccountsTag
 *
 * @property string $id
 * @property string $account_id
 * @property string $team_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsTag whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsTag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsTag whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountsTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FbAccountsTag extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'user_id',
        'name',
        'team_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
