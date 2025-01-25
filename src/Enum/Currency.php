<?php

declare(strict_types=1);

namespace Shipy\Enum;

enum Currency: string
{
    case TRY = 'TRY';
    case EUR = 'EUR';
    case USD = 'USD';
    case GBP = 'GBP';
} 