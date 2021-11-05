<?php

namespace App\Http\Controllers;

use App\Http\Resources\FbAccountResource;
use App\Models\FbAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

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
            'tags.*' => 'string'
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

        $tags = collect($request->get('tags'))
            ->transform(fn($tag) => [
                'name' => $tag,
                'team_id' => Auth::user()->team->id
            ]);

        $account->tags()->createMany($tags);

        return new FbAccountResource($account->load('tags'));
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

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\FbAccount $fbAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(FbAccount $fbAccount)
    {
        //
    }
}
