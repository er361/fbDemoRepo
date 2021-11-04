<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|confirmed|min:6'
        ]);
//        if ($this->can('update', User::class))
            Auth::user()->update(['password' => bcrypt($request->get('password'))]);
//        else
//            abort(403);
    }
}
