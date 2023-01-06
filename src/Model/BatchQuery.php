<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Validator\Constraints as Assert;

class BatchQuery implements BatchQueryInterface
{
    #[Assert\Valid]
    private array $sorting = [];

    #[Assert\Count(min: 1)]
    #[Assert\Valid]
    private array $ids = [];

    public function getSorting(): array
    {
        return $this->sorting;
    }

    public function setSorting(array $sorting)
    {
        $this->sorting = $sorting;
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function setIds(array $ids)
    {
        $this->ids = $ids;
    }
}
