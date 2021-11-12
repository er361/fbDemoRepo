<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\ListRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\FbAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
                    $request->has('username'),
                    fn(Builder $q) => $q->orderBy('username', $request->input('username'))
                );

                $builder->when(
                    $request->has('display_name'),
                    fn(Builder $q) => $q->orderBy('display_name', $request->input('username'))
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
            })->paginate($request->get('perPage', ListRequest::PER_PAGE_DEFAULT));
        return UserResource::collection($users);
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

    public function addTags(Request $request)
    {
        $this->authorize('create', User::class);
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid',
            'tags' => 'array|required',
            'tags.*.name' => 'string|max:255'
        ]);

        $tags = collect($request->get('tags'))->transform(fn($tag) => [
            'name' => $tag['name'],
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
            'tags.*.name' => 'string|max:255'
        ]);
        $tags = collect($request->get('tags'));
        User::query()->whereIn('id', $request->get('ids'))
            ->ownTeam()
            ->each(function ($user) use ($request, $tags) {
                /**
                 * @var $user FbAccount
                 */

                $user->tags()
                    ->whereIn('name', $tags->pluck('name')->toArray())
                    ->delete();
            });
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
