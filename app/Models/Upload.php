<?php

namespace App\Models;

use App\Enums\ConversionStatuses;
use App\Enums\JSONStructures;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int                id
 * @property int                user_id
 * @property CarbonImmutable    created_at
 * @property CarbonImmutable    updated_at
 * @property string             name
 * @property JSONStructures     conversion_strategy
 * @property ConversionStatuses status
 * @property bool               is_private
 * @property string             csv_path
 * @property string             json_path
 */
class Upload extends Model
{
    const string TABLE = 'uploads';

    public const int RULE_MAX_NAME = 60;

    protected $table = self::TABLE;

    protected $casts = [
        'is_private' => 'boolean',
        'conversion_strategy' => JSONStructures::class,
        'status' => ConversionStatuses::class,
    ];

    public function authorName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_private ? null : $this->author->name,
        )->shouldCache();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
