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
use Chevere\Components\Variable\Exceptions\VariableExportException;
use Chevere\Components\Variable\VariableExport;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouteableContract;

/**
 * Determines if a RouteContract is able to be routed.
 * @package Chevere\Components\Router
 */
final class Routeable implements RouteableContract
{
    private RouteContract $route;

    /**
     * {@inheritdoc}
     */
    public function __construct(RouteContract $route)
    {
        $this->route = $route;
        $this->assertExportable();
        $this->assertMethodControllerNames();
    }

    /**
     * {@inheritdoc}
     */
    public function route(): RouteContract
    {
        return $this->route;
    }

    private function assertExportable(): void
    {
        try {
            new VariableExport($this->route);
        } catch (VariableExportException $e) {
            throw new RouteableException(
                (new Message("Instance of %className% is not exportable: %message%"))
                    ->code('%className%', RouteContract::class)
                    ->code('%message%', $e->getMessage())
                    ->toString()
            );
        }
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
