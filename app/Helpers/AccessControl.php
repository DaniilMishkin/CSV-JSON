<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

final readonly class AccessControl
{
    /**
     * @throws AuthorizationException
     */
    public static function checkAccess(string $permission, $arguments = [], string $message = ''): void
    {
        try {
            Gate::authorize($permission, $arguments);
        } catch (AuthorizationException $e) {
            if (!empty($message)) {
                throw new AuthorizationException($message, $e->getCode(), $e);
            } else {
                throw $e;
            }
        }
    }

    public static function hasAccess(string $permission, $arguments = []): bool
    {
        return Auth::user()->can($permission, $arguments);
    }
}
