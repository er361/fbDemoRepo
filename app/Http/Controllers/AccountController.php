<?php

namespace App\Http\Controllers;

use App\Http\Resources\FbAccountResource;
use App\Models\FbAccount;
use App\Models\Proxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        //
        $this->validate($request, [
            'sort' => 'array',
            'sort.name' => 'in:asc,desc',
            'sort.status' => 'in:asc,desc',
            'filters' => 'array',
            'filters.status' => 'in:NEW,TOKEN_ERROR,ACTIVE',
            'filters.archived' => 'boolean',
            'filters.user_id' => 'array',
            'filters.user_id.*' => 'uuid',
            'filters.name' => 'string|max:255',
            'filters.tags' => 'array',
            'filters.tags.*' => 'string|max:255',
            'perPage' => 'integer'
        ]);

        $accounts = FbAccount::query()
            ->when($request->has('sort'), function (Builder $query) use ($request) {
                $query->when(
                    $request->has('sort.name'),
                    fn($q) => $q->orderBy('name', $request->input('sort.name'))
                )->when(
                    $request->has('sort.status'),
                    fn($q) => $q->orderBy('status', $request->input('sort.status'))
                );
            })->when($request->has('filters'), function (Builder $query) use ($request) {
                //status
                $query->when(
                    $request->has('filters.status'),
                    fn(Builder $q) => $q->where('status', $request->input('filters.status'))
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
                //name
                $query->when(
                    $request->has('filters.name'),
                    fn(Builder $q) => $q->whereRaw(
                        sprintf("match(name) against('%s')", $request->input('filters.name'))
                    )
                );
                //tags
                $query->when(
                    $request->has('filters.tags'),
                    fn(Builder $q) => $q->whereHas(
                        'tags',
                        fn(Builder $q) => $q->whereIn('name', $request->input('filters.tags'))
                    )
                );
            })->paginate($request->get('perPage'));

        return FbAccountResource::collection($accounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return FbAccountResource
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name' => 'required|string',
            'access_token' => 'required|string',
            'business_access_token' => 'string',
            'password' => 'string',
            'user_agent' => 'string',
            'cookies' => 'json',
            'tags' => 'array',
            'tags.*' => 'string',
            'proxy_id' => 'uuid',
            'proxy' => 'array',
            'proxy.port' => 'required_with:proxy|integer',
            'proxy.type' => 'required_with:proxy|in:http,https,socks5,socks4,ssh',
            'proxy.name' => 'required_with:proxy|string',
            'proxy.host' => 'required_with:proxy|string',
            'proxy.login' => 'required_with:proxy|string',
        ]);

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
        return new FbAccountResource($account->load('tags', 'proxy'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\FbAccount $fbAccount
     * @return \Illuminate\Http\Response
     */
    public
    function show(
        FbAccount $fbAccount
    ) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\FbAccount $fbAccount
     * @return FbAccountResource
     */
    public
    function update(
        Request $request,
        FbAccount $fbAccount
    ) {
        //
        $this->validate($request, [
            'name' => 'string|max:255',
            'useragent' => 'string',
            'tags' => 'array',
            'tags.*' => 'string',
            'access_token' => 'string',
            'business_access_token' => 'string',
            'notes' => 'string', //todo sanitize
            'proxy_id' => 'uuid',
            'proxy' => 'array',
            'proxy.port' => 'required_with:proxy|integer',
            'proxy.type' => 'required_with:proxy|in:http,https,socks5,socks4,ssh',
            'proxy.name' => 'required_with:proxy|string',
            'proxy.host' => 'required_with:proxy|string',
            'proxy.login' => 'required_with:proxy|string',
        ]);


        if ($request->has('proxy')) {
            $proxy = $this->createProxy($request);
            $fbAccount->proxy()->associate($proxy);
        }

        $tags = $this->createTags($request);

        $fbAccount->tags()->createMany($tags);
        $fbAccount->update($request->all());
        $fbAccount->refresh();

        return new FbAccountResource($fbAccount->load('proxy', 'tags'));
    }

    public
    function changeProxy(
        Request $request
    ) {
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

        FbAccount::query()->whereIn('id', $request->get('ids'))
            ->each(function ($account) use ($request) {
                /**
                 * @var $account FbAccount
                 */
                if ($request->has('proxy_id')) {
                    $account->proxy_id = $request->get('proxy_id');
                }

                if ($request->has('proxy')) {
                    $proxy = $this->createProxy($request);
                    $account->proxy()->associate($proxy);
                }
                $account->save();
            });
    }

    public
    function addTags(
        Request $request
    ) {
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

    public
    function removeTags(
        Request $request
    ) {
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid',
            'tags' => 'array|required',
            'tags.*.name' => 'string|max:255'
        ]);
        $tags = collect($request->get('tags'));
        FbAccount::query()->whereIn('id', $request->get('ids'))
            ->each(function ($account) use ($request, $tags) {
                /**
                 * @var $account FbAccount
                 */

                $account->tags()
                    ->whereIn('name', $tags->pluck('name')->toArray())
                    ->delete();
            });
    }

    public
    function deleteBulk(
        Request $request
    ) {
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid'
        ]);
        FbAccount::query()->whereIn('id', $request->get('ids'))
            ->where('user_id', Auth::id())
            ->delete();
    }

    public
    function archiveBulk(
        Request $request
    ) {
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid'
        ]);
        FbAccount::query()->whereIn('id', $request->get('ids'))
            ->where('user_id', Auth::id())
            ->update(['archived' => true]);
    }

    public
    function unArchiveBulk(
        Request $request
    ) {
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid'
        ]);

        FbAccount::query()->whereIn('id', $request->get('ids'))
            ->where('user_id', Auth::id())
            ->update(['archived' => false]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public
    function createTags(
        Request $request
    ): \Illuminate\Support\Collection {
        $tags = collect($request->get('tags'))
            ->transform(fn($tag) => [
                'name' => $tag,
                'team_id' => Auth::user()->team->id
            ]);
        return $tags;
    }

    /**
     * @param Request $request
     * @return Builder|Model
     */
    public
    function createProxy(
        Request $request
    ): Builder|Model {
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
}
