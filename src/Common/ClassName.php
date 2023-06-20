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

namespace Chevere\Common;

use Chevere\Common\Interfaces\ClassNameInterface;
use Chevere\String\StringAssert;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use function Chevere\Message\message;

final class ClassName implements ClassNameInterface
{
    public function __construct(
        private string $name
    ) {
        (new StringAssert($this->name))->notEmpty()->notCtypeSpace();
        $this->assertExists($this->name);
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

        throw new TypeError(
            message('Class %name% must implement %interface% interface')
                ->withCode('%name%', $this->name)
                ->withCode('%interface%', $class)
        );
    }

    private function assertExists(string $name): void
    {
        if (class_exists($name)) {
            return;
        }

        throw new InvalidArgumentException(
            message("Class %name% doesn't exists")
                ->withCode('%name%', $name)
        );
    }
}
