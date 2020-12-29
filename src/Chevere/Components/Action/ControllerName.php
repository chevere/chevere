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

namespace Chevere\Components\Action;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Action\ControllerInterface;
use Chevere\Interfaces\Action\ControllerNameInterface;

final class ControllerName implements ControllerNameInterface
{
    private string $string;

    public function __construct(string $name)
    {
        $this->string = $name;
        $this->assertController();
    }

    public function toString(): string
    {
        return $this->string;
    }

    private function assertController(): void
    {
        if (! class_exists($this->string)) {
            throw new InvalidArgumentException(
                (new Message("Controller %controllerName% doesn't exists"))
                    ->code('%controllerName%', $this->string),
                100
            );
        }
        if (! is_subclass_of($this->string, ControllerInterface::class)) {
            throw new InvalidArgumentException(
                (new Message('Controller %controllerName% must implement the %interface% interface'))
                    ->code('%controllerName%', $this->string)
                    ->code('%interface%', ControllerInterface::class),
                101
            );
        }
    }
}
