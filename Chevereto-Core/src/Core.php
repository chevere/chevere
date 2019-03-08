<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
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
     * @param string $string String you want to namespace.
     *
     * @return string Namespaced string.
     */
    public static function namespaced(string $string) : string
    {
        return CORE_NS_HANDLE . $string;
    }
    /**
     * Returns the class file path.
     *
     * @param mixed $class Class name (string) or Class instance (object).
     *
     * @return string $filename Class file path.
     */
    public static function getClassFilename($class) : string
    {
        if ($filename = (new ReflectionClass($class))->getFileName()) {
            return $filename;
        } else {
            throw new Exception('Unable to retrieve class filename.');
        }
    }
    /**
     * Returns script execution time at NOW.
     *
     * @return float Script execution time in microseconds.
     */
    public static function execTime() : float
    {
        return microtime(true) - TIME_BOOTSTRAP;
    }
}