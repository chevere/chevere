<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Router;

use LogicException;
use Throwable;

use Chevere\Components\Message\Message;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\Traits\RouteAccessTrait;

final class Resolver
{
    use RouteAccessTrait;

    public function __construct(string $serialized)
    {
        try {
            $this->route = unserialize($serialized, ['allowed_classes' => [Route::class]]);
        } catch (Throwable $e) {
            throw new LogicException(
                (new Message('Unable to unserialize: %message%'))
                    ->code('%message%', $e->getMessage())
                    ->toString()
            );
        }
    }
}
