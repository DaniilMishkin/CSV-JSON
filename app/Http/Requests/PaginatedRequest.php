<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Spatie\LaravelData\Data;

class PaginatedRequest extends Data
{
    public const int DEFAULT_PER_PAGE = 10;

    public int $page = 1;
    public int $perPage = self::DEFAULT_PER_PAGE;

    public ?string $sortBy = null;
    public ?string $sortDirection = null;
}
