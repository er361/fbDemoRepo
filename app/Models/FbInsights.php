<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FbInsights
 *
 * @property string $id
 * @property string $ad_object_id
 * @property string $level
 * @property string $date
 * @property string $impressions
 * @property string $spend
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsights newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsights newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsights query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsights whereAdObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsights whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsights whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsights whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsights whereImpressions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsights whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsights whereSpend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbInsights whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FbInsights extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'id',
        'ad_object_id',
        'level',
        'date',
        'team_id',
        'user_id',
        'impressions',
        'spend'
    ];
}
