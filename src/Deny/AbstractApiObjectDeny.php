<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Deny;

abstract class AbstractApiObjectDeny extends AbstractObjectDeny
{
    public const GET = 'get';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    public function deny(string $denyKey, object $object): ?string
    {
        $method = $denyKey.'Deny';
        if (!method_exists($this, $method)) {
            // We will allow skiping definition of deny methods for predefined denyKeys
            $allowedMissingMethodDenyKeys = [
                self::GET,
                self::EDIT,
                self::DELETE,
            ];

            if (in_array($denyKey, $allowedMissingMethodDenyKeys)) {
                // In such case we will return null by default
                return null;
            }

            throw new \Exception('Method "'.$method.'" does not exists');
        }

        return $this->$method($object);
    }

    protected function modifyFillDenyResult(string $denyResult, string $denyKey, DenyInterface $object): string
    {
        $reflectionClass = new \ReflectionClass($object);

        return 'deny.'.lcfirst($reflectionClass->getShortName()).'.'.$denyResult;
    }
}
