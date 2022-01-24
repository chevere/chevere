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
final class PatchMethod implements MethodInterface
{
    public static function name(): string
    {
        return 'PATCH';
    }

    public static function description(): string
    {
        return 'Apply partial modifications described in the request entity to the target resource.';
    }
}
