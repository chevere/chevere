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

namespace Chevereto\Chevere;

use Chevereto\Chevere\Utils\Str;

abstract class Load
{
    const INCLUDE = 'include';
    const INCLUDE_ONCE = 'include_once';

    /**
     * Includes PHP based on the given arguments.
     *
     * @param string $filepath    path to the file to include
     * @param array  $vars        injected variables
     * @param string $constructor constructor to use
     *
     * @return mixed
     *
     * Don't require(), it stops execution and mess the custom error handler
     */
    public static function php(string $filepath, array $vars = null, string $constructor = self::INCLUDE)
    {
        if (!Str::endsWith('.php', $filepath)) {
            $filepath = Path::fromHandle($filepath);
        }
        // Extract varName => varValue
        if (is_array($vars)) {
            extract($vars);
        }
        switch ($constructor) {
            case static::INCLUDE:
                return include $filepath;
            case static::INCLUDE_ONCE:
                return include_once $filepath;
        }
    }
}
