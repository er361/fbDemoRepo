<?php

namespace App\Policies;

use App\Models\FbAccount;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FbAccountPolicy
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
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\FbAccount $fbAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, FbAccount $fbAccount)
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
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\FbAccount $fbAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, FbAccount $fbAccount)
    {
        if ($user->role == User::ROLE_ADMIN) {
            return true;
        }

        if ($fbAccount->user_id == $user->id) {
            return true;
        }

        $permission = $fbAccount->permissions()->firstWhere([
            'to_user_id' => Auth::user()->id,
            'type' => FbAccount::PERMISSION_TYPE_ACTIONS
        ]);

        if ($permission) {
            return true;
        }

        if ($user->role == User::ROLE_TEAM_LEAD) {
            $subordinatesIds = $user->subordinates()->pluck('id');
            $actionsPermissionToSubordinates = $fbAccount->permissions()
                ->whereIn('to_user_id', $subordinatesIds)
                ->where('type', FbAccount::PERMISSION_TYPE_ACTIONS)
                ->exists();
            $inSubordinatesId = in_array($fbAccount->user_id, $subordinatesIds->toArray());
            return $actionsPermissionToSubordinates || $inSubordinatesId;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\FbAccount $fbAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, FbAccount $fbAccount)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\FbAccount $fbAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, FbAccount $fbAccount)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\FbAccount $fbAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, FbAccount $fbAccount)
    {
        //
    }
}
