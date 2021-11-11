<?php

namespace App\Policies;

use App\Models\FbAccount;
use App\Models\Proxy;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class ProxyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Proxy $proxy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Proxy $proxy)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Proxy $proxy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Proxy $proxy)
    {
        if ($user->role == User::ROLE_ADMIN) {
            return true;
        }

        if ($proxy->user_id == $user->id) {
            return true;
        }

        $permission = $proxy->permissions()->firstWhere([
            'to_user_id' => Auth::user()->id,
            'type' => Proxy::PERMISSION_ADMIN
        ]);

        if ($permission) {
            return true;
        }

        if ($user->role == User::ROLE_TEAM_LEAD) {
            $subordinatesIds = $user->subordinates()->pluck('id');
            $actionsPermissionToSubordinates = $proxy->permissions()
                ->whereIn('to_user_id', $subordinatesIds)
                ->where('type', Proxy::PERMISSION_ADMIN)
                ->exists();
            $inSubordinatesId = in_array($proxy->user_id, $subordinatesIds->toArray());
            return $actionsPermissionToSubordinates || $inSubordinatesId;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Proxy $proxy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Proxy $proxy)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Proxy $proxy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Proxy $proxy)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Proxy $proxy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Proxy $proxy)
    {
        //
    }
}
