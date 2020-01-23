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

namespace Chevere\Components\App\Instances;

use LogicException;
use Chevere\Components\Screen\Container;

/**
 * A container for the application Runtime.
 */
final class ScreenContainerInstance
{
    private static Container $instance;

    public function __construct(Container $screens)
    {
        self::$instance = $screens;
    }

    public static function get(): Container
    {
        if (!isset(self::$instance)) {
            throw new LogicException('No screen container instance present');
        }

        return self::$instance;
    }
}
