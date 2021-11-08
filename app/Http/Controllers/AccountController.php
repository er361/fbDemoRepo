<?php

namespace App\Http\Controllers;

use App\Http\Resources\FbAccountResource;
use App\Models\FbAccount;
use App\Models\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'proxy.port' => 'integer',
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
            $proxyData = array_merge(
                $request->get('proxy'),
                [
                    'user_id' => Auth::id(),
                    'team_id' => Auth::user()->team->id
                ]
            );

            $proxy = Proxy::query()->create($proxyData);
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
    public function show(FbAccount $fbAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\FbAccount $fbAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FbAccount $fbAccount)
    {
        //
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
            ->where('user_id', Auth::id())
            ->delete();
    }

    public function archiveBulk(Request $request)
    {
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid'
        ]);
        FbAccount::query()->whereIn('id', $request->get('ids'))
            ->where('user_id', Auth::id())
            ->update(['archived' => true]);
    }

    public function unArchiveBulk(Request $request)
    {
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
    public function createTags(Request $request): \Illuminate\Support\Collection
    {
        $tags = collect($request->get('tags'))
            ->transform(fn($tag) => [
                'name' => $tag,
                'team_id' => Auth::user()->team->id
            ]);
        return $tags;
    }
}
