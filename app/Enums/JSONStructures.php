<?php

namespace App\Enums;

use App\Traits\Enums\HasValues;

enum JSONStructures: string
{
    use HasValues;

    case Flat = 'flat';
    case Tree = 'tree';
}
