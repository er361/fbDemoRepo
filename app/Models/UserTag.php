<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserTag
 *
 * @property string $id
 * @property string $user_id
 * @property string $team_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\UserTagFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTag whereUserId($value)
 * @mixin \Eloquent
 */
class UserTag extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'user_id',
        'team_id',
        'name'
    ];
}
