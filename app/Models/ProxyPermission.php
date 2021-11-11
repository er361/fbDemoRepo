<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProxyPermission extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'proxy_id',
        'team_id',
        'from_user_id',
        'to_user_id',
        'type'
    ];
}
