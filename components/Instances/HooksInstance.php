<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Instances;

use Chevere\Components\Hooks\Hooks;
use LogicException;

final class HooksInstance
{
    private static Hooks $instance;

    public function __construct(Hooks $hooks)
    {
        if (isset(self::$instance)) {
            // throw new LogicException('This instance can be only created once');
        }
        self::$instance = $hooks;
    }

    public static function get(): Hooks
    {
        if (!isset(self::$instance)) {
            throw new LogicException('No hook instance present');
        }

        return self::$instance;
    }
}
