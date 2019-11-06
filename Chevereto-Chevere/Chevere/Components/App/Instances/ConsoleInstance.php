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

use Chevere\Components\App\Instances\Traits\AssertInstanceTrait;
use Chevere\Contracts\Console\ConsoleContract;

/**
 * A container for the built-in console.
 */
final class ConsoleInstance
{
    use AssertInstanceTrait;

    /** @var ConsoleContract */
    private static $instance;

    public function __construct(ConsoleContract $console)
    {
        self::$instance = $console;
    }

    public static function type(): string
    {
        return ConsoleContract::class;
    }

    public static function get(): ConsoleContract
    {
        self::assertInstance();
        
        return self::$instance;
    }
}
