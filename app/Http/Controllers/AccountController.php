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
    public function show(FbAccount $fbAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\FbAccount $fbAccount
     * @return FbAccountResource
     */
    public function update(Request $request, FbAccount $fbAccount)
    {
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

    /**
     * @param Request $request
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
}
