<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * favoritedは　micropost_id の Micropost は user_id のUser からお気に入りされている
     *
     */
    public function favorited()
    {
        // 第一引数：関係するModelクラス　
        // 第二引数：中間テーブル　
        // 第三引数：中間テーブルに保存されている自分のidを示すカラム名　
        // 第四引数：中間テーブルに保存されている関係先のidを示すカラム名
        return $this->belongsToMany(User::class, 'favorites', 'micropost_id', 'user_id')->withTimestamps();
    }
}
