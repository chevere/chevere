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

namespace Chevere\Writer;

use Chevere\Throwable\Exceptions\LogicException;
use Chevere\Writer\Interfaces\WritersInterface;
use function Chevere\Message\message;

final class WritersInstance
{
    private static ?WritersInterface $instance;

    public function __construct(WritersInterface $writers)
    {
        self::$instance = $writers;
    }

    public static function get(): WritersInterface
    {
        if (! isset(self::$instance)) {
            throw new LogicException(
                message('No writers instance present')
            );
        }

        return self::$instance;
    }
}
