<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FbAccountApp
 *
 * @property string $id
 * @property int $app_id
 * @property string $fb_ad_account_id
 * @property string $team_id
 * @property string $user_id
 * @property string $name
 * @property string $logo_url
 * @property array $supported_platforms
 * @property array $object_store_urls
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp query()
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp whereFbAdAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp whereObjectStoreUrls($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp whereSupportedPlatforms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FbApp whereUserId($value)
 * @mixin \Eloquent
 */
class FbApp extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'app_id',
        'fb_ad_account_id',
        'team_id',
        'user_id',
        'name',
        'logo_url',
        'supported_platforms',
        'object_store_urls'
    ];

    protected $casts = [
        'supported_platforms' => 'json',
        'object_store_urls' => 'json'
    ];
}
