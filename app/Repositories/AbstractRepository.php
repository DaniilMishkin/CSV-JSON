<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Helpers\AccessControl;
use App\Http\Requests\PaginatedRequest;
use App\Http\Responses\ReferenceResponse;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Spatie\LaravelData\PaginatedDataCollection;

abstract class AbstractRepository
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

    protected function prepareWhereLikeValue(string $rawValue): string
    {
        return sprintf('%%%s%%', $this->sanitizeString($rawValue));
    }

    protected function sanitizeString(string $rawValue): string
    {
        return trim(preg_replace('#[\'\"`%]+#ui', '', $rawValue));
    }

    /**
     * @throws UnauthorizedException
     */
    protected function user(): ?User
    {
        return Auth::getUser();
    }

    protected function paginate(
        Builder $query,
        PaginatedRequest $request,
        string $mapTo = ReferenceResponse::class): PaginatedDataCollection
    {
        $paginator = $query->paginate(perPage: $request->perPage, page: $request->page);

        return $mapTo::collect($paginator, PaginatedDataCollection::class);
    }
}
