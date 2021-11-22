<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\ListRequest;
use App\Http\Resources\V1\ProxyResource;
use App\Models\Proxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;


class ProxyController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Proxy::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(ListRequest $request)
    {
        $this->validate($request, [
            'sort' => 'array',
            'sort.name' => 'in:asc,desc',
            'sort.type' => 'in:asc,desc',
            'filters' => 'array',
            'filters.name' => 'string|max:255'
        ]);

        $proxies = Proxy::query()
            ->actionsByRole()
            ->when($request->has('sort'), function (Builder $builder) use ($request) {
                $builder->when(
                    $request->has('sort.name'),
                    fn(Builder $q) => $q->orderBy('name', $request->input('sort.name'))
                );
                $builder->when(
                    $request->has('sort.type'),
                    fn(Builder $q) => $q->orderBy('type', $request->input('sort.type'))
                );
            })
            ->when($request->has('filters'), function (Builder $builder) use ($request) {
                $builder->when(
                    $request->has('filters.name'),
                    fn(Builder $q) => $q->whereRaw(
                        sprintf("match(name) against('%s')", $request->input('filters.name'))
                    )
                );
            })
            ->paginate($request->get('perPage', 10));

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
            'proxies.*.name' => 'nullable|string',
            'proxies.*.host' => 'required|string',
            'proxies.*.port' => 'required|integer',
            'proxies.*.login' => 'nullable|string',
            'proxies.*.password' => 'nullable|string',
            'proxies.*.change_ip_url' => 'nullable|string'
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
        $this->authorize('update', $proxy);
        $proxy->check();

        return \response()->json([
            'data' => [
                'success' => true,
                'external_ip' => $proxy->external_ip
            ]
        ]);
    }

    public function addPermissions(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'uuid',
            'permissions' => 'required|array',
            'permissions.*.to_user_id' => 'uuid',
            'permissions.*.type' => 'in:admin'
        ]);

        $permissions = $request->collect('permissions')
            ->transform(function ($permission) {
                $permission['team_id'] = Auth::user()->team_id;
                $permission['from_user_id'] = Auth::id();
                return $permission;
            });

        Proxy::query()
            ->actionsByRole()
            ->whereIn('id', $request->get('ids'))
            ->each(function (Proxy $proxy) use ($request, $permissions) {
                $permissions->each(function ($permission) use ($proxy) {
                    try {
                        $proxy->permissions()->create($permission);
                    } catch (QueryException $exception) {
                        Log::warning('duplicate proxy permission - ' . $exception->getMessage());
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
            'permissions.*.type' => 'in:admin'
        ]);

        $permissions = $request->collect('permissions');
        Proxy::query()
            ->actionsByRole()
            ->whereIn('id', $request->get('ids'))
            ->each(function (Proxy $proxy) use ($request, $permissions) {
                $proxy->permissions()
                    ->whereIn('to_user_id', $permissions->pluck('to_user_id')->toArray())
                    ->whereIn('type', $permissions->pluck('type')->toArray())
                    ->delete();
            });
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
            'type' => 'nullable|string|in:http,https,socks5,socks4,ssh',
            'name' => 'nullable|string',
            'host' => 'nullable|string',
            'port' => 'nullable|integer',
            'change_ip_url' => 'nullable|string',
            'expiration_date' => 'nullable|date'
        ]);

        $proxy->update($validatedData);
        $proxy->refresh();


        return new ProxyResource($proxy);
    }

    public function deleteBulk(Request $request)
    {
        $this->validate($request, [
            'ids' => 'array|required',
            'ids.*' => 'uuid'
        ]);
        Proxy::query()->whereIn('id', $request->get('ids'))
            ->actionsByRole()
            ->delete();
        return response()->json(['success' => true]);
    }
}
