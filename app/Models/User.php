<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User（Eloquent Model）
 * 
 * Infrastructure層のORMモデル
 * Domain層のUserエンティティとは別物
 * 
 * NOTE: クラス名は Laravel の規約に従い「User」としています
 *       Domain層の User エンティティと混同しないよう注意が必要です
 *       - このクラス: App\Models\User (Eloquent ORM)
 *       - Domain層: App\Domain\UserAggregate\Entity\User (Domain Entity)
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * このUserに紐づくTodos
     */
    public function todos()
    {
        return $this->hasMany(TodoModel::class, 'user_id');
    }
}
