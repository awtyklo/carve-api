<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Enum;

enum ListQuerySortingDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';
}
