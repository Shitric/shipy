<?php

declare(strict_types=1);

namespace Shipy\Enum;

enum PaymentMethod: string
{
    case CREDIT_CARD = 'CC';
    case MOBILE = 'Mobile';
} 