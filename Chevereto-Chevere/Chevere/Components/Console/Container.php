<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Console;

/**
 * A container for the built-in console.
 */
final class Container
{
    private static $instance;

    public function __construct()
    {
        static::$instance = new Console();
    }

    public static function hasInstance(): bool
    {
        return isset(static::$instance);
    }

    public static function getInstance(): Console
    {
        return static::$instance;
    }
}
