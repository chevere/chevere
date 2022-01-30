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

namespace Chevere\Http\Methods;

use Chevere\Http\Interfaces\MethodInterface;

/**
 * @codeCoverageIgnore
 */
final class GetMethod implements MethodInterface
{
    public static function name(): string
    {
        return 'GET';
    }

    public static function description(): string
    {
        return 'Transfer a current representation of the target resource.';
    }
}
