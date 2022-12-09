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

namespace Chevere\Parameter\Traits;

use Chevere\Message\Interfaces\MessageInterface;
use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\BooleanParameterInterface;
use Chevere\Parameter\Interfaces\FileParameterInterface;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Throwable\Errors\TypeError;

trait ParametersGetTypesTrait
{
    abstract public function get(string $name): ParameterInterface;

    public function getArray(string $name): ArrayParameterInterface
    {
        try {
            /** @var ArrayParameterInterface */
            return $this->get($name);
            // @phpstan-ignore-next-line
        } catch (\TypeError $e) {
            throw new TypeError(
                $this->getTypeError(ArrayParameterInterface::class)
            );
        }
    }

    public function getBoolean(string $name): BooleanParameterInterface
    {
        try {
            /** @var BooleanParameterInterface */
            return $this->get($name);
            // @phpstan-ignore-next-line
        } catch (\TypeError $e) {
            throw new TypeError(
                $this->getTypeError(BooleanParameterInterface::class)
            );
        }
    }

    public function getFile(string $name): FileParameterInterface
    {
        try {
            /** @var FileParameterInterface */
            return $this->get($name);
            // @phpstan-ignore-next-line
        } catch (\TypeError $e) {
            throw new TypeError(
                $this->getTypeError(FileParameterInterface::class)
            );
        }
    }

    public function getFloat(string $name): FloatParameterInterface
    {
        try {
            /** @var FloatParameterInterface */
            return $this->get($name);
            // @phpstan-ignore-next-line
        } catch (\TypeError $e) {
            throw new TypeError(
                $this->getTypeError(FloatParameterInterface::class)
            );
        }
    }

    public function getInteger(string $name): IntegerParameterInterface
    {
        try {
            /** @var IntegerParameterInterface */
            return $this->get($name);
            // @phpstan-ignore-next-line
        } catch (\TypeError $e) {
            throw new TypeError(
                $this->getTypeError(IntegerParameterInterface::class)
            );
        }
    }

    public function getObject(string $name): ObjectParameterInterface
    {
        try {
            /** @var ObjectParameterInterface */
            return $this->get($name);
            // @phpstan-ignore-next-line
        } catch (\TypeError $e) {
            throw new TypeError(
                $this->getTypeError(ObjectParameterInterface::class)
            );
        }
    }

    public function getString(string $name): StringParameterInterface
    {
        try {
            /** @var StringParameterInterface */
            return $this->get($name);
            // @phpstan-ignore-next-line
        } catch (\TypeError $e) {
            throw new TypeError(
                $this->getTypeError(StringParameterInterface::class)
            );
        }
    }

    private function getTypeError(string $expected): MessageInterface
    {
        return message('Parameter is not of type %expected%')
            ->withTranslate('%expected%', $expected);
    }
}
