<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Helper;

class MessageParameterNormalizer
{
    /**
     * Every message parameter from default constraints (i.e. Symfony\Component\Validator\Constraints\GreaterThan)
     * are put in double brackets with spaces (to avoid accidental replacement when applying parameters).
     * We want to remove the double brackets and send them as it is and let parameter replacement be handled as needed by other application.
     */
    public static function normalize(array $parameters): array
    {
        $parameterKeys = array_keys($parameters);
        $normalizedParameterKeys = array_map(function ($parameterKey) {
            return self::normalizeParameter($parameterKey);
        }, $parameterKeys);

        return array_combine($normalizedParameterKeys, $parameters);
    }

    /**
     * Every message parameter from default constraints (i.e. Symfony\Component\Validator\Constraints\GreaterThan)
     * are put in double brackets with spaces (to avoid accidental replacement when applying parameters).
     * We want to remove the double brackets and send them as it is and let parameter replacement be handled as needed by other application.
     */
    public static function normalizeParameter(string $parameter): string
    {
        return str_replace(['{{ ', ' }}'], '', $parameter);
    }
}
