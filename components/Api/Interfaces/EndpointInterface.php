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

namespace Chevere\Components\Api\Interfaces;

interface EndpointInterface
{
    // /api/articles/{id}
    public function key(): string;

    // /api/articles/{id} -> id ?? ''
    public function id(): string;

    public function withMethod(EndpointMethodInterface $method): EndpointInterface;
}
