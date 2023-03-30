<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

use Carve\ApiBundle\Enum\BatchResultMessageSeverity;
use Carve\ApiBundle\Enum\BatchResultStatus;
use Symfony\Component\Serializer\Annotation\Groups;

class BatchResult implements BatchResultInterface
{
    #[Groups(['id', 'identification', 'representation', 'batchResult'])]
    private ?int $id = null;

    #[Groups(['id', 'identification', 'representation', 'batchResult'])]
    private ?string $representation = null;

    #[Groups(['id', 'identification', 'representation', 'batchResult'])]
    private ?BatchResultStatus $status = null;

    #[Groups(['id', 'identification', 'representation', 'batchResult'])]
    private ?array $messages = [];

    public function addMessage(string $message, ?array $parameters = [], ?BatchResultMessageSeverity $severity = null): void
    {
        $this->messages[] = new BatchResultMessage($message, $parameters, $severity);
    }

    public function addMessageError(string $message, ?array $parameters = []): void
    {
        $this->addMessage($message, $parameters, BatchResultMessageSeverity::ERROR);
    }

    public function addMessageWarning(string $message, ?array $parameters = []): void
    {
        $this->addMessage($message, $parameters, BatchResultMessageSeverity::WARNING);
    }

    public function clearMessages(): void
    {
        $this->messages = [];
    }

    public function __construct($object, BatchResultStatus $status, ?string $message = null, ?array $parameters = [], ?BatchResultMessageSeverity $severity = BatchResultMessageSeverity::ERROR)
    {
        $this->status = $status;

        if ($message) {
            $this->addMessage($message, $parameters, $severity);
        }

        if (method_exists($object, 'getId')) {
            $this->id = $object->getId();
        }

        if (method_exists($object, 'getRepresentation')) {
            $this->representation = $object->getRepresentation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id)
    {
        $this->id = $id;
    }

    public function getRepresentation(): ?string
    {
        return $this->representation;
    }

    public function setRepresentation(?string $representation)
    {
        $this->representation = $representation;
    }

    public function getStatus(): ?BatchResultStatus
    {
        return $this->status;
    }

    public function setStatus(?BatchResultStatus $status)
    {
        $this->status = $status;
    }

    public function getMessages(): ?array
    {
        return $this->messages;
    }

    public function setMessages(?array $messages)
    {
        $this->messages = $messages;
    }
}
