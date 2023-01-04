<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

interface BatchQueryInterface
{
    public function getSorting(): array;

    public function getIds(): array;
}
