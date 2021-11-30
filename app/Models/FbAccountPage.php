<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FbAccountPage
 *
 * @property string $id
 * @property string $page_id
 * @property string $fb_account_id
 * @property string $access_token
 * @property int $is_published
 * @property array $picture
 * @property string $name
 * @property string $category
 * @property array $category_list
 * @property array $tasks
 * @property array|null $cover
 * @property string $team_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereCategoryList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereFbAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage wherePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbAccountPage whereUserId($value)
 * @mixin \Eloquent
 */
class FbAccountPage extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'fb_account_id',
        'page_id',
        'access_token',
        'is_published',
        'picture',
        'name',
        'category',
        'category_list',
        'tasks',
        'cover',
        'team_id',
        'user_id',
    ];

    protected $casts = [
        'picture' => 'json',
        'category_list' => 'json',
        'tasks' => 'json',
        'cover' => 'json'
    ];
}
