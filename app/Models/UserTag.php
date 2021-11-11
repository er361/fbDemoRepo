<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTag extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'user_id',
        'team_id',
        'name'
    ];
}
