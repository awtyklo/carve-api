<?php

declare(strict_types=1);

namespace Carve\ApiBundle\Helper;

class Arr
{
    /**
     * Check whether $value is array accessible.
     */
    public static function accessible($value): bool
    {
        return is_array($value) || $value instanceof \ArrayAccess;
    }

    /**
     * Return the first element in an array passing a given truth test (callback).
     *
     * Example:
     * $cat = Arr::first(fn ($animal) => $animal['name'] === 'cat', $animals);
     */
    public static function first($array, callable $callback, $default = null)
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Return the first key for a value in an array passing a given truth test (callback).
     *
     * Example:
     * $catKey = Arr::first(fn ($animal) => $animal['name'] === 'cat', $animals);
     */
    public static function firstKey($array, callable $callback, $default = null)
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $key;
            }
        }

        return $default;
    }

    /**
     * Check whether given $key (does NOT support dot notation) exists in provided array.
     */
    public static function exists($array, $key)
    {
        if ($array instanceof \ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Check whether given $keys (one or multiple keys, supports dot notation) exists in provided array.
     */
    public static function has($array, string|array $keys): bool
    {
        $keys = (array) $keys;

        if (!$array || [] === $keys) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get item by given $key (dot notation) from provided array.
     */
    public static function get($array, string $key, $default = null)
    {
        if (!static::accessible($array)) {
            return $default;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (!str_contains($key, '.')) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }
}
