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

namespace Chevere\Controller;

use Chevere\Controller\Interfaces\HttpMiddlewareInterface;
use Chevere\DataStructure\Traits\VectorTrait;
use Psr\Http\Server\MiddlewareInterface;

final class HttpMiddleware implements HttpMiddlewareInterface
{
    use VectorTrait;

    /**
     * @var array<MiddlewareInterface>
     */
    private array $vector = [];

    public function __construct(MiddlewareInterface ...$middleware)
    {
        $this->vector = $middleware;
        $this->count = count($middleware);
    }

    public function withAppend(MiddlewareInterface ...$middleware): self
    {
        $new = clone $this;
        foreach ($middleware as $item) {
            $new->vector[] = $item;
            $new->count++;
        }

        return $new;
    }

    public function withPrepend(MiddlewareInterface ...$middleware): self
    {
        $new = clone $this;
        foreach ($middleware as $item) {
            array_unshift($new->vector, $item);
            $new->count++;
        }

        return $new;
    }
}
