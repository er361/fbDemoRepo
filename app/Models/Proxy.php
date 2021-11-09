<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proxy extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    protected $table = 'proxies';
    protected $fillable = [
        'team_id',
        'user_id',
        'type',
        'name',
        'host',
        'port',
        'login',
        'password',
        'change_ip_url',
        'status',
        'external_ip',
        'change_ip_url',
        'expiration_date',
    ];
}
