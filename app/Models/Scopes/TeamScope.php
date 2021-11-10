<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TeamScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        $builder->where('team_id', Auth::user()->team_id);
    }
}
