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
use Chevere\Controller\Traits\ControllerAccessTrait;

/**
 * Api provides a static method to read the exposed API inside the app runtime.
 */
final class Method implements MethodContract
{
    use ControllerAccessTrait;

    /** @var string HTTP request method */
    private $method;

    /** @var string A ControllerContract name */
    private $controllerName;

    public function __construct(string $method)
    {
        $this->setMethod($method);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function withController(string $controllerName): MethodContract
    {
        $new = clone $this;
        if (!is_subclass_of($controllerName, ControllerContract::class)) {
            throw new InvalidArgumentException(
                (new Message('Controller %s must implement the %i interface.'))
                    ->code('%s', $controllerName)
                    ->code('%i', ControllerContract::class)
                    ->toString()
            );
        }
        $new->controllerName = $controllerName;

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
}
