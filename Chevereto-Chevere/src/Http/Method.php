<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Http;

use InvalidArgumentException;
use Chevere\Message\Message;
use Chevere\Contracts\Http\MethodContract;
use Chevere\Contracts\Controller\ControllerContract;
use Chevere\Controller\Traits\ControllerStringAccessTrait;

/**
 * Api provides a static method to read the exposed API inside the app runtime.
 */
final class Method implements MethodContract
{
    use ControllerStringAccessTrait;

    /** @param string HTTP request method */
    private $method;

    public function __construct(string $method)
    {
        $this->setMethod($method);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function withController(string $controller): MethodContract
    {
        $new = clone $this;
        $new->setController($controller);

        return $new;
    }

    private function setMethod(string $method)
    {
        if (!in_array($method, MethodContract::ACCEPT_METHODS)) {
            throw new InvalidArgumentException(
                (new Message('Unknown HTTP method %s.'))
                    ->code('%s', $method)
                    ->toString()
            );
        }
        $this->method = $method;
    }

    private function setController(string $controller)
    {
        if (!is_subclass_of($controller, ControllerContract::class)) {
            throw new InvalidArgumentException(
                (new Message('Controller %s must implement the %i interface.'))
                    ->code('%s', $controller)
                    ->code('%i', ControllerContract::class)
                    ->toString()
            );
        }
        $this->controller = $controller;
    }
}
