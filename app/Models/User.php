<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int             id
 * @property CarbonImmutable created_at
 * @property CarbonImmutable updated_at
 * @property string          name_first
 * @property string          name_last
 * @property string          email
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const string TABLE = 'users';

    public const int RULE_MAX_NAME = 60;
    public const int RULE_MAX_EMAIL = 100;

    protected $table = self::TABLE;

    protected $fillable = [
        'name_first',
        'name_last',
        'email',
        'created_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn () => sprintf('%s %s', $this->name_first, $this->name_last),
        )->shouldCache();
    }
}
