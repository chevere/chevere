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

use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exceptions\RouteableException;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouteableContract;

final class Routeable implements RouteableContract
{
    /** @var RouteContract */
    private $route;

    /**
     * {@inheritdoc}
     */
    public function __construct(RouteContract $route)
    {
        $this->route = $route;
        $this->assertMethodControllerNames();
    }

    /**
     * {@inheritdoc}
     */
    public function route(): RouteContract
    {
        return $this->route;
    }

    private function assertMethodControllerNames(): void
    {
        if (!$this->route->hasMethodControllerNameCollection()) {
            throw new RouteableException(
                (new Message("Instance of %className% doesn't contain any method controller"))
                    ->code('%className%', RouteContract::class)
                    ->toString()
            );
        }
    }
}
