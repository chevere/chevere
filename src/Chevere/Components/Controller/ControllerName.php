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

namespace Chevere\Components\Controller;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Interfaces\Controller\ControllerNameInterface;

final class ControllerName implements ControllerNameInterface
{
    public function __construct(
        private string $name
    ) {
        $this->assertController();
    }

    public function toString(): string
    {
        return $this->name;
    }

    private function assertController(): void
    {
        if (! class_exists($this->name)) {
            throw new InvalidArgumentException(
                (new Message("Controller %controllerName% doesn't exists"))
                    ->code('%controllerName%', $this->name),
                100
            );
        }
        if (! is_subclass_of($this->name, ControllerInterface::class)) {
            throw new InvalidArgumentException(
                (new Message('Controller %controllerName% must implement the %interface% interface'))
                    ->code('%controllerName%', $this->name)
                    ->code('%interface%', ControllerInterface::class),
                101
            );
        }
    }
}
