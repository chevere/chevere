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

namespace Chevere\Components\Http\Methods;

use Chevere\Components\Http\Interfaces\MethodInterface;

final class TraceMethod implements MethodInterface
{
    public static function name(): string
    {
        return 'TRACE';
    }

    public static function description(): string
    {
        return 'Perform a message loop-back test along the path to the target resource';
    }
}
