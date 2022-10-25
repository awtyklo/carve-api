<?php

declare(strict_types=1);

namespace Carve\ApiBundle\View;

class ExportCsvView
{
    /**
     * Results serialized by FosRestBundle.
     */
    private $results;

    /**
     * Array of Carve\ApiBundle\Model\ExportQueryField.
     */
    private array $fields = [];

    private ?string $filename = null;

    public function __construct($results, array $fields, string $filename)
    {
        $this->results = $results;
        $this->fields = $fields;
        $this->filename = $filename;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function setResults(array $results)
    {
        $this->results = $results;
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
