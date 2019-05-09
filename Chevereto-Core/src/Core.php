<?php

declare(strict_types=1);
/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use Exception;
use ReflectionClass;

class Core
{
    /**
     * Returns a string preceded by core namespace.
     *
     * @param string $string string you want to namespace
     *
     * @return string namespaced string
     */
    public static function namespaced(string $string): string
    {
        return CORE_NS_HANDLE.$string;
    }

    /**
     * Returns the class file path.
     *
     * @param mixed $class class name (string) or Class instance (object)
     *
     * @return string $filename class file path
     */
    public static function getClassFilename($class): string
    {
        $filename = (new ReflectionClass($class))->getFileName();
        if (false === $filename) {
            throw new Exception(
                (string) (new Message('Class %s is defined in the PHP core or in a extension.'))
                    ->code('%s', $class)
            );
        }

        return $filename;
    }

    /**
     * Returns script execution time at NOW.
     *
     * @return float script execution time in microseconds
     */
    public static function execTime(): float
    {
        return microtime(true) - TIME_BOOTSTRAP;
    }
}
