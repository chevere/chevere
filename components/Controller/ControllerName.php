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

use Chevere\Components\Controller\Exceptions\ControllerInterfaceException;
use Chevere\Components\Controller\Exceptions\ControllerNotExistsException;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Interfaces\ControllerNameInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Str\StrAssert;
use Throwable;

final class ControllerName implements ControllerNameInterface
{
    private string $string;

    public function __construct(string $name)
    {
        $this->string = $name;
        $this->assertName();
        $this->assertController();
    }

    public function toString(): string
    {
        return $this->string;
    }

    private function assertName(): void
    {
        (new StrAssert($this->string))
            ->notEmpty()
            ->notCtypeSpace()
            ->notContains(' ');
    }

    private function assertController(): void
    {
        if (!class_exists($this->string)) {
            throw new ControllerNotExistsException(
                (new Message("Controller %controllerName% doesn't exists"))
                    ->code('%controllerName%', $this->string)
                    ->toString()
            );
        }
        if (!is_subclass_of($this->string, ControllerInterface::class)) {
            throw new ControllerInterfaceException(
                (new Message('Controller %controllerName% must implement the %interface% interface'))
                    ->code('%controllerName%', $this->string)
                    ->code('%interface%', ControllerInterface::class)
                    ->toString()
            );
        }
    }
}
