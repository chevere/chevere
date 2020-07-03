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

use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Message\Message;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Http\MethodInterface;
use Chevere\Interfaces\Route\RouteEndpointInterface;
use OutOfBoundsException;

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
        foreach ($controller->parameters()->getGenerator() as $parameter) {
            $this->parameters[$parameter->name()] = [
                'name' => $parameter->name(),
                'regex' => $parameter->regex()->toNoDelimiters(),
                'description' => $parameter->description(),
                'isRequired' => $parameter->isRequired(),
            ];
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

    /**
     *
     * @throws OutOfBoundsException if $parameter doesn't exists
     */
    public function withoutParameter(string $parameter): RouteEndpointInterface
    {
        if (array_key_exists($parameter, $this->parameters) === false) {
            throw new OutOfBoundsException(
                (new Message("Parameter %parameter% doesn't exists"))
                    ->code('%parameter%', $parameter)
                    ->toString()
            );
        }
        $new = clone $this;
        unset($new->parameters[$parameter]);

        return $new;
    }

    public function parameters(): array
    {
        return $this->parameters;
    }
}
