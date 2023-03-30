<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Enum\BatchResultMessageSeverity;

interface BatchResultMessageInterface
{
    public function getMessage(): ?string;

    public function getParameters(): ?array;

    public function getSeverity(): ?BatchResultMessageSeverity;
}
