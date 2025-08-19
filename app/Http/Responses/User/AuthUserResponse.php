<?php

declare(strict_types=1);

namespace App\Http\Responses\User;

use Spatie\LaravelData\Data;

class AuthUserResponse extends Data
{
    public int $id;
    public string $email;
    public string $name;
}
