<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Enum;

enum BatchResultStatus: string
{
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case SKIPPED = 'skipped';
    case ERROR = 'error';
}
