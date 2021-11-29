<?php

namespace App\Models;

use App\Events\Logout;
use App\Events\UserCreatedEvent;
use App\Listeners\CreateTeamForUser;
use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User
 *
 * @property string $id
 * @property string|null $team_id
 * @property string $username
 * @property string $role
 * @property string|null $display_name
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $subordinates
 * @property-read int|null $subordinates_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserTag[] $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\Tarif|null $tarif
 * @property-read \App\Models\Team|null $team
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $teamleads
 * @property-read int|null $teamleads_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static Builder|User ownTeam()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereDisplayName($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRole($value)
 * @method static Builder|User whereTeamId($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, Uuid, SoftDeletes;

    const ROLE_ADMIN = 'admin';
    const ROLE_TEAM_LEAD = 'teamlead';
    const ROLE_USER = 'user';
    const ROLE_FARMER = 'farmer';


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable
        = [
            'id',
            'team_id',
            'display_name',
            'username',
            'password',
            'role'
        ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeOwnTeam(Builder $query)
    {
        return $query->where('team_id', Auth::user()->team_id);
    }

    public function tarif()
    {
        return $this->hasOne(Tarif::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function tags()
    {
        return $this->hasMany(UserTag::class);
    }

    public function teamleads()
    {
        return $this->belongsToMany(
            User::class,
            'user_teamlead',
            'user_id',
            'teamlead_id'
        );
    }

    public function subordinates()
    {
        return $this->belongsToMany(
            User::class,
            'user_teamlead',
            'teamlead_id',
            'user_id'
        );
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected static function booted()
    {
        static::created(function ($user) {
            if (empty($user->team_id)) {
                // Создать команду
                $user->team()->create(['name' => $user->username]);

                // Прописать ID команды пользователю
                $user->team_id = $user->team->id;
                $user->save();
            }
        });
    }

    public function invalidateToken()
    {
        $token = JwtToken::whereUserId($this->id)->first()?->token;
        if (!$token) {
            return;
        }

        \JWTAuth::setToken($token);
        \JWTAuth::invalidate();
        Logout::dispatch($this->id);
    }
}
