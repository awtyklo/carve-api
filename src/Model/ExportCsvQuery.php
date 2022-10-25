<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Validator\Constraints as Assert;

class ExportCsvQuery implements ExportCsvQueryInterface
{
    #[Assert\Valid]
    private array $sorting = [];

    #[Assert\Valid]
    private array $filters = [];

    #[Assert\Valid]
    private array $fields = [];

    #[Assert\NotBlank]
    private ?string $filename = null;

    public function getSorting(): array
    {
        return $this->sorting;
    }

    public function setSorting(array $sorting)
    {
        $this->sorting = $sorting;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename)
    {
        $this->filename = $filename;
    }
}
