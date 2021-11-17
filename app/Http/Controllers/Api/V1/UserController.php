<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\Logout;
use App\Http\Requests\ListRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\FbAccount;
use App\Models\Team;
use App\Models\User;
use App\Models\UserTag;
use Barryvdh\Reflection\DocBlock\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Namshi\JOSE\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    public function index(ListRequest $request)
    {
        $this->validate($request, [
            'sort' => 'array',
            'sort.username' => 'in:asc,desc',
            'sort.display_name' => 'in:asc,desc',
            'filters' => 'array',
            'filters.with_trashes' => 'boolean',
            'filters.tags' => 'array',
            'filters.tags.*' => 'string|max:255'
        ]);

        $users = User::query()
            ->ownTeam()
            ->when($request->has('sort'), function (Builder $builder) use ($request) {
                $builder->when(
                    $request->has('sort.username'),
                    fn(Builder $q) => $q->orderBy('username', $request->input('sort.username'))
                );

                $builder->when(
                    $request->has('sort.display_name'),
                    fn(Builder $q) => $q->orderBy('display_name', $request->input('sort.display_name'))
                );
            })->when($request->has('filters'), function (Builder $builder) use ($request) {
                $builder->when($request->has('filters.with_trashes'), fn(Builder $q) => $q->withTrashed());
                $builder->when(
                    $request->has('filters.tags'),
                    fn(Builder $q) => $q->whereHas(
                        'tags',
                        fn($q) => $q->whereIn('name', $request->input('filters.tags'))
                    )
                );
            })->with('tags', fn(HasMany $q) => $q->select('name', 'user_id'))
            ->with('teamleads:id,username')
            ->paginate(
                $request->get('perPage', ListRequest::PER_PAGE_DEFAULT)
            );

        return UserResource::collection($users);
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'role' => 'in:admin,user,farmer,teamlead',
            'username' => 'email',
            'display_name' => 'nullable|string',
            'password' => 'string|min:6',
            'teamleads' => 'array',
            'teamleads.*' => 'uuid'
        ]);

        if ($request->has('teamleads')) {
            $this->checkTeamleads($request);
            $user->teamleads()->sync($request->get('teamleads'));
        } else {
            $user->teamleads()->detach();
        }


        if ($request->has('role')) {
            if (Team::whereFounderId($user->id)->exists()) {
                abort(422, 'Нельзя сменить роль основателю команды');
            }

            if ($user->id == Auth::id()) {
                abort(422, 'Нельзя сменить роль самому себе');
            }
        }

        $requestData = $request->collect();

        if ($request->has('password') &&
            !Hash::check($request->password, $user->password)
        ) {
            $requestData->put('password', bcrypt($request->password));
            $user->invalidateToken();
        }


        $user->update($requestData->all());

        return new UserResource($user);
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
            'display_name' => 'string|nullable',
            'password' => 'required|string|min:6',
            'teamleads' => 'array',
            'teamleads.*' => 'uuid'
        ]);

        if ($request->has('teamleads')) {
            $this->checkTeamleads($request);
        }

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

        if ($request->has('teamleads')) {
            $user->teamleads()->sync($request->get('teamleads'));
        }


        return new UserResource($user->refresh());
    }

    public function addTags(Request $request)
    {
        $this->authorize('create', User::class);

        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid',
            'tags' => 'array|required',
            'tags.*' => 'string|max:255'
        ]);

        $tags = collect($request->get('tags'))->transform(fn($tag) => [
            'name' => $tag,
            'team_id' => Auth::user()->team_id
        ]);


        User::query()->whereIn('id', $request->get('ids'))
            ->ownTeam()
            ->each(function ($user) use ($request, $tags) {
                /**
                 * @var $user FbAccount
                 */
                $accountTagsNames = $user->tags()->pluck('name');
                $tags->each(function ($tag) use ($accountTagsNames, $user) {
                    if (!in_array($tag['name'], $accountTagsNames->toArray())) {
                        $user->tags()->create($tag);
                    }
                });
            });
    }

    public function removeTags(Request $request)
    {
        $this->authorize('delete-bulk', User::class);
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid',
            'tags' => 'array|required',
            'tags.*' => 'string|max:255'
        ]);
        User::query()->whereIn('id', $request->get('ids'))
            ->ownTeam()
            ->each(function ($user) use ($request) {
                /**
                 * @var $user FbAccount
                 */

                $user->tags()
                    ->whereIn('name', $request->get('tags'))
                    ->delete();
            });
    }

    public function tags(Request $request)
    {
        return UserTag::whereTeamId(Auth::user()->team_id)
            ->pluck('name');
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

    public function restoreBulk(Request $request)
    {
        $this->authorize('restore', User::class);

        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'uuid'
        ]);

        User::onlyTrashed()->whereIn('id', $request->ids)
            ->restore();
    }

    /**
     * @param Request $request
     */
    public function checkTeamleads(Request $request): void
    {
        if ($request->get('teamleads')
            && $request->has('role')
            && $request->get('role') !== User::ROLE_USER
        ) {
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
    }
}
