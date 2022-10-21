<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

interface ExportCsvQueryInterface extends ExportQueryInterface
{
    public function getFields(): array;

    public function getFilename(): ?string;
}
