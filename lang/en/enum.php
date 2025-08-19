<?php

use App\Enums\ConversionStatuses;
use App\Enums\JSONStructures;

return [
    JSONStructures::class => [
        JSONStructures::Flat->value => 'Flat',
        JSONStructures::Tree->value => 'Tree dot',
    ],
    ConversionStatuses::class => [
        ConversionStatuses::Queued->value => 'Queued',
        ConversionStatuses::Processing->value => 'Processing',
        ConversionStatuses::Done->value => 'Done',
        ConversionStatuses::Error->value => 'Error',
    ],
];
