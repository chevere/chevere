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

namespace Chevere\Components\Route;

use Chevere\Components\App\Exceptions\MiddlewareContractException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Middleware\MiddlewareContract;

final class Middlewares
{
    /** @var array */
    private $array;

    /** @var string */
    private $middleware;

    public function __construct()
    {
        $this->array = [];
    }

    public function withAddedMiddlewareName(string $middleware): Middlewares
    {
        $this->middleware = $middleware;
        $this->assertMiddlewareContract();
        $new = clone $this;
        $new->array[] = $middleware;

        return $new;
    }

    public function get(): array
    {
        return $this->array;
    }

    private function assertMiddlewareContract(): void
    {
        if (is_subclass_of(MiddlewareContract::class, $this->middleware)) {
            throw new MiddlewareContractException(
                (new Message('Middleware %middleware% must implement the %contract% contract'))
                    ->code('%middleware%', $this->middleware)
                    ->code('%contract%', MiddlewareContract::class)
                    ->toString()
            );
        }
    }
}
