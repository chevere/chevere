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

use LogicException;
use Chevere\Components\Screen\Container;
use Chevere\Components\Screen\Interfaces\ScreenContainerInterface;
use Chevere\Components\Screen\ScreenContainer;
use Chevere\Components\Writers\Interfaces\WritersInterface;

final class WritersInstance
{
    private static WritersInterface $instance;

    public function __construct(WritersInterface $writers)
    {
        if (isset(self::$instance)) {
            throw new LogicException('This instance can be only created once');
        }
        self::$instance = $writers;
    }

    public static function get(): WritersInterface
    {
        if (!isset(self::$instance)) {
            throw new LogicException('No writers instance present');
        }

        return self::$instance;
    }
}
