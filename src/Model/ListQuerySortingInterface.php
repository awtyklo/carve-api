<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Enum\ListQuerySortingDirection;

interface ListQuerySortingInterface
{
    public function getField(): ?string;

    public function getDirection(): ?ListQuerySortingDirection;
}
