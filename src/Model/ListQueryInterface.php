<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

interface ListQueryInterface
{
    public function getPage(): ?int;

    public function getRowsPerPage(): ?int;
}
