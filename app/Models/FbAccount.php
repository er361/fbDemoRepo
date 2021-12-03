<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use App\Models\Scopes\TeamScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\FbAccount
 *
 * @property string $id
 * @property string $user_id
 * @property string $team_id
 * @property string|null $proxy_id
 * @property string $name
 * @property string|null $notes
 * @property string|null $user_agent
 * @property string $access_token
 * @property string|null $access_token_error_message
 * @property string|null $business_access_token
 * @property string|null $fbdtsg
 * @property string|null $lsd
 * @property string|null $login
 * @property string|null $password
 * @property string|null $cookies
 * @property string $status
 * @property int $activity_block
 * @property int $archived
 * @property string|null $facebook_id
 * @property string|null $facebook_profile_name
 * @property int $advertising_rules_accepted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FbAdAccount[] $adAccounts
 * @property-read int|null $ad_accounts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FbAccountsPermission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Proxy|null $proxy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FbAccountsTag[] $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\User $user
 * @method static Builder|FbAccount actionsByRole($permissionType = 'view')
 * @method static \Database\Factories\FbAccountFactory factory(...$parameters)
 * @method static Builder|FbAccount newModelQuery()
 * @method static Builder|FbAccount newQuery()
 * @method static \Illuminate\Database\Query\Builder|FbAccount onlyTrashed()
 * @method static Builder|FbAccount query()
 * @method static Builder|FbAccount whereAccessToken($value)
 * @method static Builder|FbAccount whereAccessTokenErrorMessage($value)
 * @method static Builder|FbAccount whereActivityBlock($value)
 * @method static Builder|FbAccount whereAdvertisingRulesAccepted($value)
 * @method static Builder|FbAccount whereArchived($value)
 * @method static Builder|FbAccount whereBusinessAccessToken($value)
 * @method static Builder|FbAccount whereCookies($value)
 * @method static Builder|FbAccount whereCreatedAt($value)
 * @method static Builder|FbAccount whereDeletedAt($value)
 * @method static Builder|FbAccount whereFacebookId($value)
 * @method static Builder|FbAccount whereFacebookProfileName($value)
 * @method static Builder|FbAccount whereFbdtsg($value)
 * @method static Builder|FbAccount whereId($value)
 * @method static Builder|FbAccount whereLogin($value)
 * @method static Builder|FbAccount whereLsd($value)
 * @method static Builder|FbAccount whereName($value)
 * @method static Builder|FbAccount whereNotes($value)
 * @method static Builder|FbAccount wherePassword($value)
 * @method static Builder|FbAccount whereProxyId($value)
 * @method static Builder|FbAccount whereStatus($value)
 * @method static Builder|FbAccount whereTeamId($value)
 * @method static Builder|FbAccount whereUpdatedAt($value)
 * @method static Builder|FbAccount whereUserAgent($value)
 * @method static Builder|FbAccount whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|FbAccount withTrashed()
 * @method static \Illuminate\Database\Query\Builder|FbAccount withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FbPage[] $pages
 * @property-read int|null $pages_count
 */
class FbAccount extends Model
{
    use HasFactory, SoftDeletes;

    use Uuid;

    const PERMISSION_TYPE_VIEW = 'view';
    const PERMISSION_TYPE_STAT = 'stat';
    const PERMISSION_TYPE_ACTIONS = 'actions';
    const PERMISSION_TYPE_SHARE = 'share';

    protected static function booted()
    {
        static::addGlobalScope(new TeamScope());
    }

    public function scopeActionsByRole(Builder $query, $permissionType = FbAccount::PERMISSION_TYPE_VIEW)
    {
        if (Auth::user()->role !== User::ROLE_ADMIN) {
            $query->where(function (Builder $builder) use ($permissionType) {
                $builder->where('user_id', Auth::id())
                    ->orWhereHas('permissions', fn(Builder $q) => $q->where([
                        'to_user_id' => Auth::id(),
                        'type' => $permissionType
                    ]));
            });
        }

        if (Auth::user()->role == User::ROLE_TEAM_LEAD) {
            $query->orWhereRelation('user.teamleads', 'teamlead_id', Auth::id())
                ->orWhere(function (Builder $builder) use ($permissionType) {
                    $builder->whereHas('permissions', function (Builder $builder) use ($permissionType) {
                        $builder->whereIn('to_user_id', function ($builder) {
                            $builder->select('user_id')
                                ->from('user_teamlead')
                                ->where('teamlead_id', Auth::id());
                        })->where('type', $permissionType);
                    });
                });
        }

        return $query;
    }

    protected $fillable = [
        'user_id',
        'team_id',
        'proxy_id',
        'name',
        'access_token',
        'business_access_token',
        'login',
        'password',
        'user_agent',
        'cookies',
        'archived',
        'facebook_id',
        'status',
        'notes'
    ];

    public function tags()
    {
        return $this->hasMany(FbAccountsTag::class, 'account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proxy()
    {
        return $this->belongsTo(Proxy::class);
    }

    public function permissions()
    {
        return $this->hasMany(FbAccountsPermission::class, 'account_id');
    }

    public function adAccounts()
    {
        return $this->hasMany(FbAdAccount::class, 'fb_account_id');
    }

    public function pages()
    {
        return $this->hasMany(FbPage::class, 'fb_account_id');
    }

    public function getFlatRelations()
    {
        $this->load('adAccounts', 'adAccounts.campaigns');

        $adAccounts = $this->adAccounts;
        $campaigns = collect();
        $adsets = collect();
        $ads = collect();

        $this->adAccounts->each(function (FbAdAccount $adAccount) use (&$campaigns) {
            $campaigns = $campaigns->concat($adAccount->campaigns);
        });

        $this->load('adAccounts.campaigns.adsets');

        $this->adAccounts->each(function (FbAdAccount $adAccount) use (&$adsets) {
            $adAccount->campaigns->each(function (FbCampaign $campaign) use (&$adsets) {
                $adsets = $adsets->concat($campaign->adsets);
            });
        });

        $this->load('adAccounts.campaigns.adsets.ads');

        $this->adAccounts->each(function (FbAdAccount $adAccount) use (&$ads) {
            $adAccount->campaigns->each(function (FbCampaign $campaign) use (&$ads) {
                $campaign->adsets->each(function (FbAdset $adset) use (&$ads) {
                    $ads = $ads->concat($adset->ads);
                });
            });
        });

        return [
            'adAccounts' => $adAccounts,
            'campaigns' => $campaigns,
            'adsets' => $adsets,
            'ads' => $ads
        ];
    }
}
