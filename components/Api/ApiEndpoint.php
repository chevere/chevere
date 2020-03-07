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

use Chevere\Components\Api\Interfaces\ApiEndpointInterface;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Route\RouteEndpoint;

// @codeCoverageIgnore
abstract class ApiEndpoint extends RouteEndpoint implements ApiEndpointInterface
{
    abstract public function getController(): ControllerInterface;

    public function description(): string
    {
        return '';
    }

    public function parameters(): array
    {
        return [];
    }
}
