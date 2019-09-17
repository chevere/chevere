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

/**
 * Api provides a static method to read the exposed API inside the app runtime.
 */
final class Method implements MethodContract
{
    /** Array containing all the known HTTP methods. */
    const ACCEPT_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'COPY', 'HEAD', 'OPTIONS', 'LINK', 'UNLINK', 'PURGE', 'LOCK', 'UNLOCK', 'PROPFIND', 'VIEW', 'TRACE', 'CONNECT'];

    /** @param string HTTP request method */
    private $method;

    /** @param string ControllerContract */
    private $controller;

    public function __construct(string $method, string $controller)
    {
        $this->setMethod($method);
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

    private function setMethod(string $method)
    {
        if (!in_array($method, self::ACCEPT_METHODS)) {
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
