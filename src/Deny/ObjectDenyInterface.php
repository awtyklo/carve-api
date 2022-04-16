<?php

namespace Carve\ApiBundle\Deny;

interface ObjectDenyInterface
{
    public function isDenied(string $denyKey, object $object): bool;

    public function deny(string $denyKey, object $object): ?string;

    public function fillDeny(DenyInterface $object): void;
}
