<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Deny;

abstract class AbstractObjectDeny implements ObjectDenyInterface
{
    public function isDenied(string $denyKey, object $object): bool
    {
        $denyResult = $this->deny($denyKey, $object);

        return null !== $denyResult;
    }

    public function deny(string $denyKey, object $object): ?string
    {
        $method = $denyKey.'Deny';
        if (!method_exists($this, $method)) {
            throw new \Exception('Method "'.$method.'" does not exists');
        }

        return $this->$method($object);
    }

    public function fillDeny(DenyInterface $object): void
    {
        $deny = [];

        $denyKeys = $this->getDenyKeys();
        foreach ($denyKeys as $denyKey) {
            $denyResult = $this->deny($denyKey, $object);
            if (null !== $denyResult) {
                $deny[$denyKey] = $this->getDenyResultLabel($denyResult, $denyKey, $object);
            }
        }

        $object->setDeny($deny);
    }

    public function getDenyResultLabel(string $denyResult, string $denyKey, DenyInterface $object): string
    {
        return 'deny.'.$denyResult;
    }

    protected function getDenyKeys(): array
    {
        $reflectionClass = new \ReflectionClass($this);

        $voterConstants = array_diff($reflectionClass->getConstants());
        $attributes = array_values($voterConstants);

        return $attributes;
    }
}
