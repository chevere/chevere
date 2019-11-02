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

final class MiddlewareNames
{
    /** @var array */
    private $array;

    public function __construct()
    {
        $this->array = [];
    }

    public function withAddedMiddlewareName(string $middlewareName): MiddlewareNames
    {
        $middlewareName = new MiddlewareName($middlewareName);
        $new = clone $this;
        $new->array[] = $middlewareName->toString();

        return $new;
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
