<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute\Export;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ExportEnumPrefix
{
    public string $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }
}
