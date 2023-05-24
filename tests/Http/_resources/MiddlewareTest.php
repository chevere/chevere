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

namespace Chevere\Tests\Http\_resources;

use Chevere\Http\Interfaces\MiddlewareInterface;
use Chevere\Http\Traits\ClientError\StatusBadRequestTrait;
use Chevere\Http\Traits\MiddlewareTrait;

final class MiddlewareTest implements MiddlewareInterface
{
    use StatusBadRequestTrait;
    use MiddlewareTrait;
}
