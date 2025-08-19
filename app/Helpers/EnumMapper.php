<?php

namespace App\Helpers;

use App\Http\Responses\EnumResponse;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;
use UnitEnum;

final class EnumMapper implements Cast, Transformer
{
    public function __construct(
        private ?string $class = null
    ) {}

    private const CACHE_EXPIRATION_TIME = 7200;

    public static function toResponse(?UnitEnum $case): ?EnumResponse
    {
        return is_null($case) ? null : EnumResponse::from(self::toArray($case));
    }

    public static function toArray(UnitEnum $case)
    {
        return [
            'id' => $case->value,
            'name' => self::name($case),
        ];
    }

    public static function name(UnitEnum $case)
    {
        return __(sprintf('enum.%s.%s', $case::class, $case->value));
    }

    /**
     * @param class-string<UnitEnum> $enumClass
     *
     * @throws Exception
     */
    public static function list(string $enumClass): Collection
    {
        if (!class_exists($enumClass)) {
            throw new Exception($enumClass.' does not exist');
        }

        return Cache::remember(self::redisKey($enumClass), self::CACHE_EXPIRATION_TIME, function () use ($enumClass) {
            return Collection::make($enumClass::cases())
                ->transform(function ($item) {
                    return EnumMapper::toResponse($item);
                });
        });
    }

    public static function collect(Collection $collection): Collection
    {
        return EnumResponse::collect($collection);
    }

    public static function invalidateCache(string $enumClass): bool
    {
        return Cache::forget(self::redisKey($enumClass));
    }

    private static function redisKey(string $enumClass): string
    {
        return sprintf(
            'enum_labels:%s:%s',
            App::currentLocale(),
            mb_strtolower(str_replace('\\', '_', $enumClass))
        );
    }

    public function transform(DataProperty $property, mixed $value, TransformationContext $context): mixed
    {
        return self::toResponse($value);
    }

    /**
     * @throws Exception
     */
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): ?EnumResponse
    {
        if (!class_exists($this->class)) {
            throw new Exception('EnumMapper error: '.$this->class.' does not exist');
        }

        return self::toResponse($this->class::from($value));
    }
}
