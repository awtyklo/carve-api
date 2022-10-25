<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Validator\Constraints as Assert;

class ExportQueryField
{
    #[Assert\NotBlank]
    private ?string $field = null;

    #[Assert\NotBlank]
    private ?string $label = null;

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(?string $field)
    {
        $this->field = $field;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label)
    {
        $this->label = $label;
    }
}
