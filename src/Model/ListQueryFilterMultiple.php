<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Validator\Constraints as Assert;

class ListQueryFilterMultiple implements ListQueryFilterMultipleInterface
{
    #[Assert\NotBlank]
    private $value;

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}
