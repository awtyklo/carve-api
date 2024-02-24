<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Validator\Constraints;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as BaseUniqueEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class UniqueEntity extends BaseUniqueEntity
{
    public function __construct(
        $fields,
        ?string $message = 'validation.uniqueEntity',
        ?string $service = null,
        ?string $em = null,
        ?string $entityClass = null,
        ?string $repositoryMethod = null,
        ?string $errorPath = null,
        bool|string|array|null $ignoreNull = null,
        ?array $groups = null,
        $payload = null,
        array $options = []
    ) {
        parent::__construct(
            $fields,
            $message,
            $service,
            $em,
            $entityClass,
            $repositoryMethod,
            $errorPath,
            $ignoreNull,
            $groups,
            $payload,
            $options,
        );
    }

    public function validatedBy(): string
    {
        return UniqueEntityValidator::class;
    }
}
