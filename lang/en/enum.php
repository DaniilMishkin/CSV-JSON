<?php

use App\Enums\ConversionStatuses;

return [
    ConversionStatuses::class => [
        ConversionStatuses::Queued->value => 'Queued',
        ConversionStatuses::Processing->value => 'Processing',
        ConversionStatuses::Done->value => 'Done',
        ConversionStatuses::Error->value => 'Error',
    ],
];
