<?php

namespace App\Services;

use App\Helpers\AccessControl;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

abstract class AbstractService
{
    /**
     * @throws AuthorizationException
     */
    protected function checkAccess(string $permission, $arguments = [], string $message = ''): void
    {
        AccessControl::checkAccess($permission, $arguments, $message);
    }

    protected function hasAccess(string $permission, $arguments = []): bool
    {
        return AccessControl::hasAccess($permission, $arguments);
    }

    /**
     * @throws UnauthorizedException
     */
    protected function user(): ?User
    {
        return Auth::getUser();
    }
}
