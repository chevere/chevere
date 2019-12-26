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

use Chevere\Components\Runtime\Runtime;

/**
 * A container for the application Runtime.
 */
final class RuntimeInstance
{
    private static Runtime $instance;

    public function __construct(Runtime $runtime)
    {
        self::$instance = $runtime;
    }

    public static function type(): string
    {
        return Runtime::class;
    }

    public static function get(): Runtime
    {
        return self::$instance;
    }
}
