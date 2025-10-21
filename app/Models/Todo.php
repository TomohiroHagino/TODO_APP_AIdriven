<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Todo（Eloquent Model）
 * 
 * Infrastructure層のORMモデル
 * Domain層のTodoEntityとは別物
 * 
 * NOTE: クラス名は Laravel の規約に従い「Todo」としています
 *       Domain層の TodoEntity と混同しないよう注意が必要です
 *       - このクラス: App\Models\Todo (Eloquent ORM)
 *       - Domain層: App\Domain\UserAggregate\Entity\TodoEntity (Domain Entity)
 */
class Todo extends Model
{
    /**
     * テーブル名
     */
    protected $table = 'todos';

    /**
     * タイムスタンプの自動管理を無効化
     * (created_atのみ使用)
     */
    public $timestamps = false;

    /**
     * 複数代入可能な属性
     */
    protected $fillable = [
        'user_id',
        'title',
        'is_done',
        'created_at',
    ];

    /**
     * キャストする属性
     */
    protected $casts = [
        'is_done' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * このTodoが属するUser
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

