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
use Throwable;
use Chevere\Message\Message;
use Chevere\Route\Route;
use Chevere\Contracts\Route\RouteContract;

final class Resolver
{
    /** @var RouteContract */
    private $route;

    public function __construct(string $serialized)
    {
        try {
            $this->route = unserialize($serialized, ['allowed_classes' => [Route::class]]);
        } catch (Throwable $e) {
            throw new LogicException(
                (new Message('Unable to unserialize: %e'))
                    ->code('%e', $e->getMessage())
                    ->toString()
            );
        }
    }

    public function get(): RouteContract
    {
        return $this->route;
    }
}
