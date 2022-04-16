<?php

namespace Carve\ApiBundle\Deny;

interface DenyInterface
{
    public function getDeny(): ?array;

    public function setDeny(?array $deny);
}
