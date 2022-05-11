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

namespace Chevere\Controller;

use Chevere\Controller\Exceptions\ControllerNameInterfaceException;
use Chevere\Controller\Exceptions\ControllerNameNotExistsException;
use Chevere\Controller\Interfaces\ControllerInterface;
use Chevere\Controller\Interfaces\ControllerNameInterface;
use Chevere\Message\Message;

final class ControllerName implements ControllerNameInterface
{
    public function __construct(
        private string $name
    ) {
        $this->assertController();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    private function assertController(): void
    {
        if (!class_exists($this->name)) {
            throw new ControllerNameNotExistsException(
                (new Message("Controller %controllerName% doesn't exists"))
                    ->code('%controllerName%', $this->name)
            );
        }
        if (!is_subclass_of($this->name, ControllerInterface::class)) {
            throw new ControllerNameInterfaceException(
                (new Message('Controller %controllerName% must implement the %interface% interface'))
                    ->code('%controllerName%', $this->name)
                    ->code('%interface%', ControllerInterface::class)
            );
        }
    }
}