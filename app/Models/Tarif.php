<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tarif
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Tarif newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tarif newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tarif query()
 * @mixin \Eloquent
 */
class Tarif extends Model
{
    use HasFactory, Uuid;
}
