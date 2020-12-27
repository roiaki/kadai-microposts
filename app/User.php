<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    /**
     * followingsは　user_id のUser は follow_id の User をフォローしている
     *
     */
    public function followings()
    {
        // 第一引数：Modelクラス　
        // 第二引数：中間テーブル　
        // 第三引数：中間テーブルに保存されている自分のidを示すカラム名　
        // 第四引数：中間テーブルに保存されている関係先のidを示すカラム名
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    public function followers()
    {
        // 第一引数：Modelクラス　
        // 第二引数：中間テーブル　
        // 第三引数：中間テーブルに保存されている自分のidを示すカラム名　
        // 第四引数：中間テーブルに保存されている関係先のidを示すカラム名
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    
    public function follow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist || $its_me) {
            // 既にフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身かどうかの確認
        $its_me = $this->id == $userId;
    
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    /**
     * タイムライン用のマイクロポストを取得するメソッド 
     * @param
     * @return microposts テーブルの user_id カラムで $follow_user_ids の中にある ユーザid を含むもの全てを取得して return
     */
    public function feed_microposts()
    {
        // UserがフォローしているUserのidの配列を取得 pluck()は与えられた引数のテーブルのカラム名だけを抜き出す
        // toArray()で通常の配列に変換
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        
        // microposts テーブルの user_id カラムで $follow_user_ids の中にある ユーザid を含むもの全てを取得して return
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
}
