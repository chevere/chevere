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

use Chevere\Components\Message\Message;
use Chevere\Contracts\Controller\ControllerContract;
use Chevere\Contracts\Http\MethodContract;
use Chevere\Contracts\Http\MethodControllerContract;
use InvalidArgumentException;

final class MethodController implements MethodControllerContract
{
    /** @var MethodContract */
    private $method;

    /** @var string */
    private $controllerName;

    /**
     * {@inheritdoc}
     */
    public function __construct(MethodContract $method, string $controllerName)
    {
        $this->method = $method;
        $this->controllerName = $controllerName;
        $this->assertControllerName();
    }

    /**
     * {@inheritdoc}
     */
    public function method(): MethodContract
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function controllerName(): string
    {
        return $this->controllerName;
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
