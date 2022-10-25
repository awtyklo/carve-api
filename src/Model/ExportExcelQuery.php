<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Validator\Constraints as Assert;

class ExportExcelQuery implements ExportExcelQueryInterface
{
    #[Assert\Valid]
    private array $sorting = [];

    #[Assert\Valid]
    private array $filters = [];

    #[Assert\Valid]
    private array $fields = [];

    #[Assert\NotBlank]
    private ?string $filename = null;

    #[Assert\NotBlank]
    // Maximum name of a sheet in Excel is 30
    #[Assert\Length(max: 30, maxMessage: 'validation.exportExcel.sheetName.tooLong')]
    // Name of a sheet in Excel cannot contain special chars
    #[Assert\Regex(pattern: '/[\*\:\/\\\?\[\]]/', match: false, message: 'validation.exportExcel.sheetName.invalidCharacters')]
    private ?string $sheetName = null;

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

    public function getSheetName(): ?string
    {
        return $this->sheetName;
    }

    public function setSheetName(?string $sheetName)
    {
        $this->sheetName = $sheetName;
    }
}
