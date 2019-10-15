<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Http;

use InvalidArgumentException;

use Chevere\Components\Controller\Traits\ControllerNameAccessTrait;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Controller\ControllerContract;
use Chevere\Contracts\Http\MethodContract;

final class Method implements MethodContract
{
    use ControllerNameAccessTrait;

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

    public function withControllerName(string $controllerName): MethodContract
    {
        $new = clone $this;
        $new->controllerName = $controllerName;
        $new->assertControllerName();

        return $new;
    }

    private function setMethod(string $method)
    {
        if (!in_array($method, MethodContract::ACCEPT_METHODS)) {
            throw new InvalidArgumentException(
                (new Message('Unknown HTTP method %method%'))
                    ->code('%method%', $method)
                    ->toString()
            );
        }
        $this->method = $method;
    }

    private function assertControllerName(): void
    {
        if (!class_exists($this->controllerName)) {
            throw new InvalidArgumentException(
                (new Message("Controller %controller% doesn't exists"))
                    ->code('%controller%', $this->controllerName)
                    ->toString()
            );
        }
        if (!is_subclass_of($this->controllerName, ControllerContract::class)) {
            throw new InvalidArgumentException(
                (new Message('Controller %controller% must implement the %contract% interface'))
                    ->code('%controller%', $this->controllerName)
                    ->code('%contract%', ControllerContract::class)
                    ->toString()
            );
        }
    }
}
