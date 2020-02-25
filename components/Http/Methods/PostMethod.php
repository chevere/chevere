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

final class PostMethod implements MethodInterface
{
    public static function name(): string
    {
        return 'POST';
    }

    public static function description(): string
    {
        return 'Perform resource-specific processing on the request payload';
    }
}
