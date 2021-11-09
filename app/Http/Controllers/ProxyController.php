<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListRequest;
use App\Http\Resources\ProxyResource;
use App\Models\FbAccount;
use App\Models\Proxy;
use App\Rules\IpOrDNS;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use function Symfony\Component\Translation\t;

class ProxyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
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
     * @param Request $request
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

    public function import(Request $request)
    {
        $this->validate($request, [
            'proxies' => 'required|array',
            'proxies.*.type' => 'string|required|in:http,https,socks5,socks4,ssh',
            'proxies.*.name' => 'string',
            'proxies.*.host' => 'required|string',
            'proxies.*.port' => 'required|integer',
            'proxies.*.login' => 'string',
            'proxies.*.password' => 'string',
            'proxies.*.change_ip_url' => 'string'
        ]);

        $request->collect('proxies')->each(function ($proxy) {
            Proxy::query()->create(
                array_merge(
                    $proxy,
                    [
                        'user_id' => Auth::id(),
                        'team_id' => Auth::user()->team_id
                    ]
                )
            );
        });

        return \response()->json([
            'status' => true
        ], 201);
    }

    public function check(Request $request, Proxy $proxy)
    {
        if (!$proxy->check()) {
            abort(400, 'check fail');
        }

        return \response()->json([
            'data' => [
                'success' => true,
                'external_ip' => $proxy->external_ip
            ]
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param Proxy $proxy
     *
     * @return Response
     */
    public function show(Proxy $proxy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Proxy $proxy
     *
     * @return ProxyResource
     */
    public function update(Request $request, Proxy $proxy)
    {
        //
        $validatedData = $this->validate($request, [
            'type' => 'string|in:http,https,socks5,socks4,ssh',
            'name' => 'string',
            'host' => 'string',
            'port' => 'integer',
            'change_ip_url' => 'string',
            'expiration_date' => 'date'
        ]);

        $proxy->update($validatedData);
        $proxy->refresh();

        if ($request->has('expiration_date') && !$proxy->expiration_date) {
            Log::warning('Proxy expiration date not set id :' . $proxy->id);
            abort(400, 'expiration date not set');
        }

        return new ProxyResource($proxy);
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

        return response()->json(['success' => true]);
    }
}
