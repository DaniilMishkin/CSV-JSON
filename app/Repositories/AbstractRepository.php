<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Responses\ReferenceResponse;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\PaginatedDataCollection;

abstract class AbstractRepository
{
    protected function prepareWhereLikeValue(string $rawValue): string
    {
        return sprintf('%%%s%%', $this->sanitizeString($rawValue));
    }

    protected function sanitizeString(string $rawValue): string
    {
        return trim(preg_replace('#[\'\"`%]+#ui', '', $rawValue));
    }

    protected function paginate(
        Builder $query,
        int $page,
        int $perPage,
        string $mapTo = ReferenceResponse::class
    ): PaginatedDataCollection {
        $paginator = $query->paginate(perPage: $perPage, page: $page);

        return $mapTo::collect($paginator, PaginatedDataCollection::class);
    }
}
