<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\FbAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    //

    public function changePassword(Request $request, User $user)
    {
        $this->authorize('update', User::class);

        $this->validate($request, [
            'password' => 'required|string|confirmed|min:6'
        ]);

        $user->update(['password' => bcrypt($request->get('password'))]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'role' => 'required|in:admin,user,farmer,teamlead',
            'username' => 'email|required',
            'display_name' => 'string',
            'password' => 'required|string|min:6',
            'teamleads' => 'array',
            'teamleads.*' => 'uuid'
        ]);

        if ($request->get('teamleads') && $request->get('role') !== User::ROLE_USER) {
            abort(422, 'Тимлиды могут быть добалвены только для роли user');
        }

        $request->collect('teamleads')->each(function ($teamLead) {
            $teamlead = DB::table('user_teamlead')->where(['teamlead_id' => $teamLead])->first();
            if (!$teamlead) {
                abort(422, 'Тимлида с id ' . $teamLead . ' не существует');
            } else {
                if (User::query()->find($teamLead)->team_id !== Auth::user()->team_id) {
                    abort(422, 'Тимлида с id ' . $teamLead . ' не в вашей команде');
                }
            }
        });

        $user = new User();
        $user->fill(
            array_merge(
                ['id' => Str::uuid()->toString()],
                $request->except('teamleads'),
                ['password' => bcrypt($request->password)],
                ['team_id' => Auth::user()->team_id]
            )
        );

        $user->saveQuietly();

        return new UserResource($user->refresh());
    }

    public function deleteBulk(Request $request)
    {
        $this->authorize('delete-bulk', User::class);

        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid'
        ]);
        $request->collect('ids')->each(function ($id) {
            if ($id == Auth::id()) {
                abort(422, 'Нельзя удалить самого себя');
            }

            $user = User::query()->find($id);
            if (!$user) {
                return;
            }

            if ($user->team->founder_id == $user->id) {
                abort(422, 'Нельзя удалить основателя команды');
            }
        });

        User::query()->whereIn('id', $request->get('ids'))
            ->ownTeam()
            ->delete();
        return response()->json(['success' => true]);
    }
}
