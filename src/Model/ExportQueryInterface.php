<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

interface ExportQueryInterface
{
    public function getSorting(): array;

    public function getFilters(): array;
}
