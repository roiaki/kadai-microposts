<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFollowController extends Controller
{
    /**
     * User.phpの中で定義したfollowメソッドを使って、ユーザーをフォローできる
     */
    public function store(Request $request, $id)
    {
        \Auth::user()->follow($id); // user() \Illuminate\Contracts\Auth\Guard.php
        return back();
    }
    
    /**
     * User.phpの中で定義したunfollowメソッドを使って、ユーザーをアンフォローできる
     * 
     */
    public function destroy($id)
    {
        \Auth::user()->unfollow($id);
        return back();
    }
}
