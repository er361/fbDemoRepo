<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListRequest;
use App\Http\Resources\ProxyResource;
use App\Models\FbAccount;
use App\Models\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProxyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(ListRequest $request)
    {
        $proxies = Proxy::query()->where('user_id', Auth::id())
            ->paginate($request->get('perPage'));

        return ProxyResource::collection($proxies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return ProxyResource
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'string|required|in:http,https,socks5,socks4,ssh',
            'name' => 'string',
            'host' => 'required|string',
            'port' => 'required|integer',
            'login' => 'string',
            'password' => 'string',
            'change_ip_url' => 'string'
        ]);

        $proxy = Proxy::create(
            array_merge(
                $request->all(),
                [
                    'user_id' => Auth::id(),
                    'team_id' => Auth::user()->team_id
                ]
            )
        );

        return new ProxyResource($proxy);
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Proxy $proxy
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Proxy $proxy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Proxy $proxy
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proxy $proxy)
    {
        //
    }

    public function deleteBulk(Request $request)
    {
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid'
        ]);

        Proxy::query()->whereIn('id', $request->get('ids'))
            ->where('user_id', Auth::id())
            ->delete();
    }
}
