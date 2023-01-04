<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Model;

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
    private ?string $message = null;

    #[Groups(['id', 'identification', 'representation', 'batchResult'])]
    private ?array $messageVariables = null;

    public function __construct($object, BatchResultStatus $status, ?string $message = null, ?array $messageVariables = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->messageVariables = $messageVariables;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message)
    {
        $this->message = $message;
    }

    public function getMessageVariables(): ?array
    {
        return $this->messageVariables;
    }

    public function setMessageVariables(?array $messageVariables)
    {
        $this->messageVariables = $messageVariables;
    }
}
