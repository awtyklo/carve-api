<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Enum\ListQuerySortingDirection;
use Carve\ApiBundle\Validator\Constraints as Assert;

class ListQuerySorting implements ListQuerySortingInterface
{
    #[Assert\NotBlank]
    private ?string $field;

    #[Assert\NotBlank]
    private ?ListQuerySortingDirection $direction;

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(?string $field)
    {
        $this->field = $field;
    }

    public function getDirection(): ?ListQuerySortingDirection
    {
        return $this->direction;
    }

    public function setDirection(?ListQuerySortingDirection $direction)
    {
        $this->direction = $direction;
    }
}
