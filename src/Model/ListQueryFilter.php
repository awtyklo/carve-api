<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Enum\ListQueryFilterType;
use Carve\ApiBundle\Validator\Constraints as Assert;

class ListQueryFilter implements ListQueryFilterInterface
{
    #[Assert\NotBlank]
    private ?string $filterBy;

    #[Assert\NotBlank]
    private ?ListQueryFilterType $filterType;

    #[Assert\NotBlank]
    private $filterValue;

    public function getFilterBy(): ?string
    {
        return $this->filterBy;
    }

    public function setFilterBy(?string $filterBy)
    {
        $this->filterBy = $filterBy;
    }

    public function getFilterType(): ?ListQueryFilterType
    {
        return $this->filterType;
    }

    public function setFilterType(?ListQueryFilterType $filterType)
    {
        $this->filterType = $filterType;
    }

    public function getFilterValue()
    {
        return $this->filterValue;
    }

    public function setFilterValue($filterValue)
    {
        $this->filterValue = $filterValue;
    }
}
