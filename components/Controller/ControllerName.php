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

use Chevere\Components\App\Exceptions\ControllerInterfaceException;
use Chevere\Components\App\Exceptions\ControllerNotExistsException;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Interfaces\ControllerNameInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Str\StrAssert;
use InvalidArgumentException;

final class ControllerName implements ControllerNameInterface
{
    private string $name;

    /**
     * @throws InvalidArgumentException if $name doesn't exists or if it doesn't implement a ControllerInterface
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->assertController();
    }

    public function name(): string
    {
        return $this->name;
    }

    private function assertName(): void
    {
        (new StrAssert($this->name))
            ->notEmpty()
            ->notCtypeSpace()
            ->notContains(' ');
    }

    private function assertController(): void
    {
        if (!class_exists($this->name)) {
            throw new ControllerNotExistsException(
                (new Message("Controller %controllerName% doesn't exists"))
                    ->code('%controllerName%', $this->name)
                    ->toString()
            );
        }
        if (!is_subclass_of($this->name, ControllerInterface::class)) {
            throw new ControllerInterfaceException(
                (new Message('Controller %controllerName% must implement the %interface% interface'))
                    ->code('%controllerName%', $this->name)
                    ->code('%interface%', ControllerInterface::class)
                    ->toString()
            );
        }
    }
}
