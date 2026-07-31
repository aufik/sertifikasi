<?php

declare(strict_types=1);

namespace App\Enums;

enum CategoryType: string
{
    case Physical = 'physical';
    case Digital = 'digital';
    case Service = 'service';
}
