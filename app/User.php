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
     * @return $user が フォローしているUser達
     */
    public function followings()
    {
        // 第一引数：Modelクラス　
        // 第二引数：中間テーブル　
        // 第三引数：中間テーブルに保存されている自分のidを示すカラム名　
        // 第四引数：中間テーブルに保存されている関係先のidを示すカラム名
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    /**
     * Userをフォローしている User達の取得
     * @return Userをフォローしている User達の取得
     */
    public function followers()
    {
        // 第一引数：Modelクラス　
        // 第二引数：中間テーブル　
        // 第三引数：中間テーブルに保存されている自分のidを示すカラム名　
        // 第四引数：中間テーブルに保存されている関係先のidを示すカラム名
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    /**
     * フォローする
     */ 
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
    
    /**
     * フォローを外す
     */ 
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
    
    /**
     * 既にフォローしているかの確認メソッド
     */ 
    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    /**
     * タイムライン用のマイクロポストを取得するメソッド welcome.blade.phpに自分がフォローしたユーザーの投稿も表示
     * @param
     * @return microposts テーブルの user_id カラムで $follow_user_ids の中にある ユーザid を含むもの全てを取得して return
     */
    public function feed_microposts()
    {
        // UserがフォローしているUserのidの配列を取得 pluck()は与えられた引数のテーブルのカラム名だけを抜き出す。pluck 摘み取る　
        // pluck 指定したキーの全コレクション値を返す
        // toArray()で通常の配列に変換
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        
        // Micropostsインスタンスの user_id カラムで $follow_user_ids の中にある ユーザid を含むもの全てを取得して return
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    /**
     * favoritesは　user_id のUser は micropost_id の Micropost をお気に入りしている
     *
     */
    public function favorites()
    {
        // 第一引数：関係するModelクラス　
        // 第二引数：中間テーブル　
        // 第三引数：中間テーブルに保存されている自分のidを示すカラム名　
        // 第四引数：中間テーブルに保存されている関係先のidを示すカラム名
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'favorite_id')->withTimestamps();
    }
    
    /**
     * お気に入り追加メソッド
     */ 
    public function favorite($micropostId) 
    {
        // 既に投稿をお気に入りしているかの確認
        $exist = $this->is_favoriting($micropostId);
        
/*        
        // お気に入りした投稿が自分の投稿でないかの確認
        $micropost_user_id = \App\Micropost::find($id);
        $its_mine = $this->id == $micropost_user_id;
*/

        if($exist) {
            // 既にお気に入りしていればなにもしない
            return false;
        } else {
            // 未お気に入りであればお気に入りする
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
    
    /**
     * お気に入りを外すメソッド
     */
    public function unfavorite($micropostId) 
    {
        // 既に投稿をお気に入りしているかの確認
        $exist = $this->is_favoriting($micropostId);

        if($exist) {
            // 既にお気に入りしていればお気に入りを外す
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            // 未お気に入りであればおなにもしない
            return false;
        }
    }
    
    /**
     * お気に入り確認メソッド
     * 
     */ 
    public function is_favoriting($micropostId)
    {
        // where(カラム名, 検索対象条件)　where('micropost_id', $micropostId) : where micropost_id = $micropostId
//        $miId = \App\Micropost::where('micropost_id', $micropostId);
        
        return $this->favorites()->where('favorite_id', $micropostId)->exists();
    }
}
