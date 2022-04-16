<?php

namespace Carve\ApiBundle\Deny;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;

trait DenyTrait
{
    #[Groups(['deny'])]
    #[OA\Property(type: 'object', example: ['denyKey' => 'denyMessage'])]
    protected ?array $deny;

    public function getDeny(): ?array
    {
        return $this->deny;
    }

    public function setDeny(?array $deny)
    {
        $this->deny = $deny;
    }
}
