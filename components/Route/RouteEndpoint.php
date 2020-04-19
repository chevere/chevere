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

namespace Chevere\Components\Route;

use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Parameter;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;

final class RouteEndpoint implements RouteEndpointInterface
{
    private MethodInterface $method;

    private ControllerInterface $controller;

    private string $description = '';

    private array $parameters = [];

    public function __construct(MethodInterface $method, ControllerInterface $controller)
    {
        $this->method = $method;
        $this->controller = $controller;
        $this->description = $method->description();
        /** @var Parameter $parameter */
        foreach ($controller->parameters()->map() as $parameter) {
            $this->parameters[$parameter->name()] = $parameter->regex();
        }
    }

    public function method(): MethodInterface
    {
        return $this->method;
    }

    public function controller(): ControllerInterface
    {
        return $this->controller;
    }

    public function withDescription(string $description): RouteEndpointInterface
    {
        $new = clone $this;
        $new->description = $description;

        return $new;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function withoutParameter(string $parameter): RouteEndpointInterface
    {
        $new = clone $this;
        unset($new->parameters[$parameter]);

        return $new;
    }

    public function parameters(): array
    {
        return $this->parameters;
    }
}
