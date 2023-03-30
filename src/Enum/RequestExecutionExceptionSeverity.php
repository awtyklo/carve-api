<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Enum;

enum RequestExecutionExceptionSeverity: string
{
    case WARNING = 'warning';
    case ERROR = 'error';
}
