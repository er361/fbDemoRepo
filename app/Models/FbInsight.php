<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\FbInsight
 *
 * @property string $id
 * @property int $object_id
 * @property string $level
 * @property string $date
 * @property int $impressions
 * @property float $spend
 * @property string $team_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight whereImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight whereSpend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight whereUserId($value)
 * @mixin \Eloquent
 * @property string $fb_account_id
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsight whereFbAccountId($value)
 */
class FbInsight extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'fb_account_id',
        'object_id',
        'level',
        'date',
        'team_id',
        'user_id',
        'impressions',
        'spend'
    ];
}
