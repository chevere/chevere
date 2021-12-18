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

namespace Chevere\Components\Router\Route;

use Chevere\Components\Common\Traits\DescriptionTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Http\MethodInterface;
use Chevere\Interfaces\Parameter\StringParameterInterface;
use Chevere\Interfaces\Router\Route\RouteEndpointInterface;

final class RouteEndpoint implements RouteEndpointInterface
{
    private string $description = '';

    use DescriptionTrait;

    private array $parameters = [];

    public function __construct(
        private MethodInterface $method,
        private ControllerInterface $controller
    ) {
        $this->description = $controller->getDescription();
        if ($this->description === '') {
            $this->description = $method->description();
        }
        /**
         * @var StringParameterInterface $parameter
         */
        foreach ($controller->parameters()->getIterator() as $name => $parameter) {
            $attributes = $parameter->attributes()->toArray();
            $array = [
                'name' => $name,
                'regex' => $parameter->regex()->toString(),
                'description' => $parameter->description(),
                'isRequired' => $controller->parameters()->isRequired($name),
            ];
            if ($attributes !== []) {
                $array['attributes'] = implode('|', $attributes);
            }
            $this->parameters[$name] = $array;
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
        if (!array_key_exists($parameter, $this->parameters)) {
            throw new OutOfBoundsException(
                (new Message("Parameter %parameter% doesn't exists"))
                    ->code('%parameter%', $parameter)
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
