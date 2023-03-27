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

namespace Chevere\Parameter;

use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Type\typeObject;
use stdClass;

final class ObjectParameter implements ObjectParameterInterface
{
    use ParameterTrait;

    private string $className;

    private object $default;

    public function setUp(): void
    {
        $this->className = stdClass::class;
        $this->default = new stdClass();
    }

    public function className(): string
    {
        return $this->className;
    }

    public function withClassName(string $className): ObjectParameterInterface
    {
        $new = clone $this;
        $new->className = $className;
        $new->type = typeObject($className);

        return $new;
    }

    public function default(): object
    {
        return $this->default;
    }

    public function assertCompatible(ObjectParameterInterface $parameter): void
    {
        if ($this->className === $parameter->className()) {
            return;
        }

        throw new InvalidArgumentException(
            message('Parameter must be of type %type%')
                ->withCode('%type%', $this->className)
        );
    }

    private function getType(): TypeInterface
    {
        return typeObject($this->className);
    }
}
