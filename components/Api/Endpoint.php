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

namespace Chevere\Components\Api;

use Chevere\Components\Api\Interfaces\EndpointInterface;
use Chevere\Components\Api\Interfaces\EndpointMethodInterface;

final class Endpoint implements EndpointInterface
{
    private string $key;

    private array $methods = [];

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function withMethod(EndpointMethodInterface $method): EndpointInterface
    {
        $new = clone $this;
        $new->methods[] = $method;

        return $new;
    }
}
