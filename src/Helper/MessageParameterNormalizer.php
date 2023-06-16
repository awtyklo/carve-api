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

    /**
     * Apply parameters to message. Example usage:
     * $message = "Hello {{ name }}"
     * $parameters = ["name" => "John"]
     * Returns: "Hello John".
     */
    public static function applyParameters(string $message, array $parameters): string
    {
        $search = [];
        $replace = [];

        foreach ($parameters as $parameter => $value) {
            $search[] = '{{ '.$parameter.' }}';
            $replace[] = $value;
        }

        return str_replace($search, $replace, $message);
    }
}
