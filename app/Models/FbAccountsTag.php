<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbAccountsTag extends Model
{
    use HasFactory,Uuid;
    protected $fillable = [
        'user_id',
        'name',
        'team_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
