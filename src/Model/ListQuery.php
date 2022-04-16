<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Validator\Constraints as Assert;

class ListQuery implements ListQueryInterface
{
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(1)]
    private ?int $page;

    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(1)]
    private ?int $rowsPerPage;

    #[Assert\Valid]
    private array $sorting;

    #[Assert\Valid]
    private array $filters;

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page)
    {
        $this->page = $page;
    }

    public function getRowsPerPage(): ?int
    {
        return $this->rowsPerPage;
    }

    public function setRowsPerPage(?int $rowsPerPage)
    {
        $this->rowsPerPage = $rowsPerPage;
    }

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
}
