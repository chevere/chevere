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

namespace Chevere\HttpFoundation;

use LogicException;
use Chevere\Message;
use Chevere\Contracts\HttpFoundation\MethodContract;
use Chevere\Contracts\Controller\ControllerContract;

/**
 * Api provides a static method to read the exposed API inside the app runtime.
 */
final class Method implements MethodContract
{
    /** @param string HTTP request method */
    private $method;

    /** @param string ControllerContract */
    private $controller;

    public function __construct(string $method, string $controller)
    {
        $this->method = $method;
        $this->setController($controller);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function controller(): string
    {
        return $this->controller;
    }

    private function setController(string $controller)
    {
        if (!is_subclass_of($controller, ControllerContract::class)) {
            throw new LogicException(
                (new Message('Callable %s must represent a class implementing the %i interface.'))
                    ->code('%s', $controller)
                    ->code('%i', ControllerContract::class)
                    ->toString()
            );
        }
        $this->controller = $controller;
    }
}
