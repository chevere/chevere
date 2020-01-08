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

use Chevere\Components\Bootstrap\Contracts\BootstrapContract;
use LogicException;

/**
 * A container for the application bootstrap.
 */
final class BootstrapInstance
{
    private static BootstrapContract $instance;

    public function __construct(BootstrapContract $bootstrap)
    {
        self::$instance = $bootstrap;
    }

    public static function get(): BootstrapContract
    {
        if (!isset(self::$instance)) {
            throw new LogicException('No bootstrap instance present');
        }

        return self::$instance;
    }
}
