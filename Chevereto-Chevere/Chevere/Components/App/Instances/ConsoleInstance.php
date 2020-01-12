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

namespace Chevere\Components\App\Instances;

use LogicException;
use Chevere\Components\Console\Interfaces\ConsoleInterface;

/**
 * A container for the built-in console.
 */
final class ConsoleInstance
{
    private static ConsoleInterface $instance;

    public function __construct(ConsoleInterface $console)
    {
        self::$instance = $console;
    }

    public static function type(): string
    {
        return ConsoleInterface::class;
    }

    public static function get(): ConsoleInterface
    {
        if (!isset(self::$instance)) {
            throw new LogicException('No console instance present');
        }

        return self::$instance;
    }
}
