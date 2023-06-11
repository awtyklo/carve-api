<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Attribute;

/**
 * Attaches summary to the operation. Summary can include variables.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Summary
{
    /**
     * @var ?string
     */
    public $summary = null;

    public function __construct(string $summary)
    {
        $this->summary = $summary;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary)
    {
        $this->summary = $summary;
    }
}
