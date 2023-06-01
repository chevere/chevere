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

namespace Chevere\Http\Interfaces;

use Psr\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;

/**
 * Describes the component in charge of defining middleware with success status code.
 */
interface MiddlewareSuccessInterface extends ServerMiddlewareInterface
{
    public static function statusSuccess(): int;
}
