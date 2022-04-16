<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Serializer;

class CircularReferenceHandler
{
    public function __invoke($object)
    {
        $result = [];

        if (method_exists($object, 'getId')) {
            $result['id'] = $object->getId();
        }

        if (method_exists($object, 'getRepresentation')) {
            $result['representation'] = $object->getRepresentation();
        }

        return $result;
    }
}
