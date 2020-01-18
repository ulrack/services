<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Helper;

class ServiceCompilerHelper
{
    /**
     * Retrieves the scope path in array format based on a service key.
     *
     * @param string $serviceKey
     *
     * @return array
     */
    public static function getScopesFromServiceKey(
        string $serviceKey
    ): array {
        // Set the default scope to global.
        $scopes = ['*'];

        // Check whether the service can be scoped.
        if (strpos($serviceKey, '.') !== false) {
            $scopes = array_merge($scopes, explode('.', $serviceKey));

            /**
             * Pop the last scope definition.
             * It should be a name for the declaration and not a scope.
             */
            array_pop($scopes);
        }

        return $scopes;
    }

    /**
     * Merges a value into an array path.
     *
     * @param array $target
     * @param array $path
     * @param array $value
     *
     * @return array
     */
    public static function mergeIntoArrayPath(
        array $target,
        array $path,
        array $value
    ): array {
        // Build a reference variable to the path.
        $reference = &$target;
        foreach ($path as $step) {
            if (!isset($reference[$step])) {
                $reference[$step] = [];
            }

            $reference = &$reference[$step];
        }

        // Merge the value into the reference.
        $reference = array_merge_recursive($reference, $value);

        return $target;
    }
}
