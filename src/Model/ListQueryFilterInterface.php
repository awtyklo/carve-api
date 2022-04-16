<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Enum\ListQueryFilterType;

interface ListQueryFilterInterface
{
    public function getFilterBy(): ?string;

    public function getFilterType(): ?ListQueryFilterType;

    public function getFilterValue();
}
