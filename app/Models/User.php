<?php

namespace App\Models;

use App\Events\UserCreatedEvent;
use App\Listeners\CreateTeamForUser;
use App\Models\Helpers\Uuid;
use App\Models\Scopes\TeamScope;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

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
    protected $hidden
        = [
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
}
