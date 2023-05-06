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
use function Chevere\Message\message;
use Chevere\String\StringAssert;

final class ControllerName implements ControllerNameInterface
{
    public function __construct(
        private string $name
    ) {
        (new StringAssert($this->name))->notEmpty()->notCtypeSpace();
        $this->assertExists();
        $this->assertInterface(ControllerInterface::class);
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function assertInterface(string $class): void
    {
        if (is_subclass_of($this->name, $class)) {
            return;
        }

        throw new ControllerNameInterfaceException(
            message('Class %controllerName% must implement %interface% interface')
                ->withCode('%controllerName%', $this->name)
                ->withCode('%interface%', $class)
        );
    }

    private function assertExists(): void
    {
        if (class_exists($this->name)) {
            return;
        }

        throw new ControllerNameNotExistsException(
            message("Class %controllerName% doesn't exists")
                ->withCode('%controllerName%', $this->name)
        );
    }
}
