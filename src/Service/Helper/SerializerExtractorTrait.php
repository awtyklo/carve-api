<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Service\Helper;

use Symfony\Component\PropertyInfo\PropertyListExtractorInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait SerializerExtractorTrait
{
    protected PropertyListExtractorInterface $serializerExtractor;

    #[Required]
    public function setSerializerExtractor(PropertyListExtractorInterface $serializerExtractor)
    {
        $this->serializerExtractor = $serializerExtractor;
    }
}
