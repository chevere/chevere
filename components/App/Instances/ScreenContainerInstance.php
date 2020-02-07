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
use Chevere\Components\Screen\Interfaces\ScreenContainerInterface;
use Chevere\Components\Screen\ScreenContainer;

/**
 * A container for the application screens.
 */
final class ScreenContainerInstance
{
    private static ScreenContainerInterface $instance;

    public function __construct(ScreenContainerInterface $screens)
    {
        self::$instance = $screens;
    }

    public static function get(): ScreenContainerInterface
    {
        if (!isset(self::$instance)) {
            throw new LogicException('No screen container instance present');
        }

        return self::$instance;
    }
}
