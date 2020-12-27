<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // 全てのコントローラーがcounts()を使用できるように
    public function counts($user) {
        
        // Micropost の数のカウントを View で表示するときのため
        $count_microposts = $user->microposts()->count();
        
        // フォロー/フォロワー数のカウント
        $count_followings = $user->followings()->count();
        $count_followers = $user->followers()->count();


        return [
            'count_microposts' => $count_microposts,
            
            'count_followings' => $count_followings,
            'count_followers' => $count_followers,
        ];
    }
}
