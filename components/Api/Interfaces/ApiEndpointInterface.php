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

interface ApiEndpointInterface
{
    /**
     * Return a description for the endpoint explaining what it does.
     */
    public function description(): string;

    /**
     * Return an instance of ClassName, which describes the parameters required by the endpoint.
     */
    public function parameters(): array; // for now
}
