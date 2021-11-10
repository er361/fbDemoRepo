<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAccountsPermission extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'account_id',
        'from_user_id',
        'to_user_id',
        'team_id',
        'type'
    ];
}
