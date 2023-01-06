<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Enum\BatchResultStatus;

interface BatchResultInterface
{
    public function getId(): ?int;

    public function getRepresentation(): ?string;

    public function getStatus(): ?BatchResultStatus;

    public function getMessage(): ?string;

    public function getMessageVariables(): ?array;
}
