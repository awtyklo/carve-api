<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Enum\BatchResultMessageSeverity;
use Symfony\Component\Serializer\Annotation\Groups;

class BatchResultMessage implements BatchResultMessageInterface
{
    #[Groups(['id', 'identification', 'representation', 'batchResult'])]
    private ?string $message = null;

    #[Groups(['id', 'identification', 'representation', 'batchResult'])]
    private ?array $parameters = null;

    #[Groups(['id', 'identification', 'representation', 'batchResult'])]
    private ?BatchResultMessageSeverity $severity = null;

    public function __construct(?string $message = null, ?array $parameters = [], ?BatchResultMessageSeverity $severity = BatchResultMessageSeverity::ERROR)
    {
        $this->message = $message;
        $this->parameters = $parameters;
        $this->severity = $severity;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message)
    {
        $this->message = $message;
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    public function setParameters(?array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getSeverity(): ?BatchResultMessageSeverity
    {
        return $this->severity;
    }

    public function setSeverity(?BatchResultMessageSeverity $severity)
    {
        $this->severity = $severity;
    }
}
