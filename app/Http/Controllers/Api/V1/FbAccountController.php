<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\ListRequest;
use App\Http\Resources\V1\FbAccountResource;
use App\Models\FbAccount;
use App\Models\Proxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FbAccountController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(FbAccount::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request)
    {
        //
        $this->validate($request, [
            'sort' => 'array',
            'sort.name' => 'in:asc,desc',
            'sort.status' => 'in:asc,desc',
            'filters' => 'array',
            'filters.status' => 'array',
            'filters.status.*' => 'in:new,token_error,active',
            'filters.archived' => 'boolean',
            'filters.user_id' => 'array',
            'filters.user_id.*' => 'uuid',
            'filters.name' => 'string|max:255',
            'filters.tags' => 'array',
            'filters.tags.*' => 'string|max:255'
        ]);

        $accounts = FbAccount::query()
            ->when($request->has('sort'), function (Builder $query) use ($request) {
                if ($request->has('sort.name')) {
                    $query->orderBy('name', $request->input('sort.name'));
                } else {
                    if ($request->has('sort.status')) {
                        $query->orderBy('status', $request->input('sort.status'));
                    }
                }
            })->when($request->has('filters'), function (Builder $query) use ($request) {
                //status
                $query->when(
                    $request->has('filters.status'),
                    fn(Builder $q) => $q->whereIn('status', $request->input('filters.status'))
                );

                //name
                $query->when(
                    $request->has('filters.name'),
                    fn(Builder $q) => $q->whereRaw(
                        sprintf("match(name) against('%s')", $request->input('filters.name'))
                    )
                );

                //archived
                $query->when(
                    $request->has('filters.archived'),
                    fn(Builder $q) => $q->where('archived', $request->input('filters.archived'))
                );

                //user_id
                $query->when(
                    $request->has('filters.user_id'),
                    fn(Builder $q) => $q->whereIn('user_id', $request->input('filters.user_id'))
                );

                //tags
                $query->when(
                    $request->has('filters.tags'),
                    fn(Builder $q) => $q->whereHas(
                        'tags',
                        fn(Builder $q) => $q->whereIn('name', $request->input('filters.tags'))
                    )
                );
            })
            ->with('user:id,username,display_name')
            ->actionsByRole()
            ->paginate($request->get('perPage', 10));

        return FbAccountResource::collection($accounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return FbAccountResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'access_token' => 'required|string',
            'business_access_token' => 'nullable|string',
            'login' => 'nullable|string',
            'password' => 'nullable|string',
            'user_agent' => 'nullable|string',
            'cookies' => 'nullable|json',
            'tags' => 'array',
            'tags.*' => 'string',
            'proxy' => 'array',
            'proxy.type' => 'required_with:proxy|in:http,https,socks5,socks4,ssh',
            'proxy.host' => 'required_with:proxy|string',
            'proxy.port' => 'required_with:proxy|integer',
            'proxy.name' => 'nullable|string',
            'proxy.login' => 'nullable|string',
            'proxy.password' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(422, $validator->errors());
        }

        if ($request->has('proxy_id') && $request->has('proxy')) {
            return $this->jsonError(422, 'Нельзя использовать proxy_id вместе с proxy');
        }

        $account = FbAccount::query()->create(
            array_merge(
                $request->all(),
                [
                    'user_id' => Auth::id(),
                    'team_id' => Auth::user()->team->id
                ]
            )
        );

        if ($request->has('proxy')) {
            $proxy = $this->createProxy($request);
            $account->proxy()->associate($proxy);
        }

        $tags = $this->createTags($request);
        $account->tags()->createMany($tags);
        $account->save();
        return new FbAccountResource($account->load('tags', 'proxy'));
    }

    public function addPermissions(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'uuid',
            'permissions' => 'required|array',
            'permissions.*.to_user_id' => 'uuid',
            'permissions.*.type' => 'in:view,stat,actions,share'
        ]);

        $permissions = $request->collect('permissions')
            ->transform(function ($permission) {
                $permission['team_id'] = Auth::user()->team_id;
                $permission['from_user_id'] = Auth::id();
                return $permission;
            });


        FbAccount::query()
            ->actionsByRole(FbAccount::PERMISSION_TYPE_SHARE)
            ->whereIn('id', $request->get('ids'))
            ->each(function (FbAccount $account) use ($request, $permissions) {
                $permissions->each(function ($permission) use ($account) {
                    try {
                        $account->permissions()->create($permission);
                    } catch (QueryException $exception) {
                        Log::error($exception->getMessage());
                    }
                });
            });
    }

    public function removePermissions(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'uuid',
            'permissions' => 'required|array',
            'permissions.*.to_user_id' => 'uuid',
            'permissions.*.type' => 'in:view,stat,actions,share'
        ]);
        $permissions = $request->collect('permissions');
        FbAccount::query()
            ->actionsByRole(FbAccount::PERMISSION_TYPE_SHARE)
            ->whereIn('id', $request->get('ids'))
            ->each(function (FbAccount $account) use ($request, $permissions) {
                $account->permissions()
                    ->whereIn('to_user_id', $permissions->pluck('to_user_id')->toArray())
                    ->whereIn('type', $permissions->pluck('type')->toArray())
                    ->delete();
            });
    }

    public function saveNotes(Request $request, FbAccount $fbAccount)
    {
        $this->validate($request, [
            'notes' => 'required|string'
        ]);
        $fbAccount->notes = $request->get('notes');
        $fbAccount->save();
    }

    /**
     * @param Request $request
     * @param FbAccount $fbAccount
     * @return FbAccountResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, FbAccount $fbAccount)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'user_agent' => 'nullable|string',
            'tags' => 'array',
            'tags.*' => 'string',
            'access_token' => 'nullable|string',
            'business_access_token' => 'nullable|string',
            'notes' => 'nullable|string', //todo sanitize
            'proxy_id' => 'uuid',
            'proxy' => 'array',
            'proxy.type' => 'required_with:proxy|in:http,https,socks5,socks4,ssh',
            'proxy.port' => 'required_with:proxy|integer',
            'proxy.host' => 'required_with:proxy|string',
            'proxy.name' => 'string',
            'proxy.login' => 'string',
            'proxy.password' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError(422, $validator->errors());
        }

        if ($request->has('proxy_id') && $request->has('proxy')) {
            return $this->jsonError(422, 'Нельзя использовать proxy_id вместе с proxy');
        }


        if ($request->has('proxy')) {
            $proxy = $this->createProxy($request);
            $fbAccount->proxy()->associate($proxy);
        }

        $this->updateTags($request, $fbAccount);

        $fbAccount->update($request->all());
        $fbAccount->refresh();

        return new FbAccountResource($fbAccount->load('proxy', 'tags'));
    }

    public function changeProxy(Request $request)
    {
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid',
            'proxy_id' => 'uuid|required_without:proxy',
            'proxy' => 'array|required_without:proxy_id',
            'proxy.port' => 'required_with:proxy|integer',
            'proxy.type' => 'required_with:proxy|in:http,https,socks5,socks4,ssh',
            'proxy.name' => 'required_with:proxy|string',
            'proxy.host' => 'required_with:proxy|string',
            'proxy.login' => 'required_with:proxy|string',
        ]);
        $proxy = null;
        if ($request->has('proxy')) {
            $proxy = $this->createProxy($request);
        }
        FbAccount::query()->whereIn('id', $request->get('ids'))
            ->actionsByRole(FbAccount::PERMISSION_TYPE_ACTIONS)
            ->each(function ($account) use ($request, $proxy) {
                /**
                 * @var $account FbAccount
                 */
                if ($request->has('proxy_id')) {
                    $account->proxy_id = $request->get('proxy_id');
                }

                if ($request->has('proxy')) {
                    if ($proxy) {
                        $account->proxy()->associate($proxy);
                    }
                }
                $account->save();
            });
    }

    public function addTags(Request $request)
    {
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


        FbAccount::query()->whereIn('id', $request->get('ids'))
            ->actionsByRole(FbAccount::PERMISSION_TYPE_ACTIONS)
            ->each(function ($account) use ($request, $tags) {
                /**
                 * @var $account FbAccount
                 */
                $accountTagsNames = $account->tags()->pluck('name');
                $tags->each(function ($tag) use ($accountTagsNames, $account) {
                    if (!in_array($tag['name'], $accountTagsNames->toArray())) {
                        $account->tags()->create($tag);
                    }
                });
            });
    }

    public function removeTags(Request $request)
    {
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid',
            'tags' => 'array|required',
            'tags.*.name' => 'string|max:255'
        ]);
        $tags = collect($request->get('tags'));
        FbAccount::query()->whereIn('id', $request->get('ids'))
            ->actionsByRole(FbAccount::PERMISSION_TYPE_ACTIONS)
            ->each(function ($account) use ($request, $tags) {
                /**
                 * @var $account FbAccount
                 */

                $account->tags()
                    ->whereIn('name', $tags->pluck('name')->toArray())
                    ->delete();
            });
    }

    public function deleteBulk(Request $request)
    {
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid'
        ]);

        FbAccount::query()->whereIn('id', $request->get('ids'))
            ->actionsByRole(FbAccount::PERMISSION_TYPE_ACTIONS)
            ->delete();
        return response()->json(['success' => true]);
    }

    public function archiveBulk(Request $request)
    {
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid'
        ]);

        FbAccount::query()->whereIn('id', $request->get('ids'))
            ->actionsByRole(FbAccount::PERMISSION_TYPE_ACTIONS)
            ->update(['archived' => true]);

        return response()->json([
            'success' => true
        ]);
    }

    public function unArchiveBulk(Request $request)
    {
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid'
        ]);

        FbAccount::query()->whereIn('id', $request->get('ids'))
            ->actionsByRole(FbAccount::PERMISSION_TYPE_ACTIONS)
            ->update(['archived' => false]);

        return response()->json(['success' => true]);
    }

    /**
     * @param Request $request
     *
     * @return Collection
     */
    public function createTags(Request $request): Collection
    {
        $tags = collect($request->get('tags'))
            ->transform(fn($tag) => [
                'name' => $tag,
                'team_id' => Auth::user()->team->id
            ]);
        return $tags;
    }

    /**
     * @param Request $request
     *
     * @return Builder|Model
     */
    public function createProxy(Request $request): Builder|Model
    {
        $proxyData = array_merge(
            $request->get('proxy'),
            [
                'user_id' => Auth::id(),
                'team_id' => Auth::user()->team->id
            ]
        );

        $proxy = Proxy::query()->create($proxyData);
        return $proxy;
    }

    /**
     * @param Request $request
     * @param FbAccount $fbAccount
     */
    public function updateTags(Request $request, FbAccount $fbAccount): void
    {
        $tags = $this->createTags($request);
        $fbAccount->tags()->delete();
        $fbAccount->tags()->createMany($tags);
    }
}
