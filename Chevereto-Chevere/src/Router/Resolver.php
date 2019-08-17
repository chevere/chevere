<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Router;

use LogicException;
use Chevere\Message;
use Chevere\Route\Route;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\ResolverContract;

final class Resolver
{
    /** @var RouteContract */
    private $route;

    public function __construct(string $serialized)
    {
        if (is_string($serialized)) {
            $this->route = unserialize($serialized, ['allowed_classes' => [Route::class]]);
        } else {
            throw new LogicException(
                (new Message('Unexpected type %t in routes table %h.'))
                    ->code('%t', gettype($serialized))
                    ->toString()
            );
        }
    }

    public function get(): RouteContract
    {
        return $this->route;
    }
}
