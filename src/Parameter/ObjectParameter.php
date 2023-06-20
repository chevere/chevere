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

use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Parameter\Traits\SchemaTrait;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Type\Interfaces\TypeInterface;
use stdClass;
use function Chevere\Message\message;
use function Chevere\Type\typeObject;

final class ObjectParameter implements ObjectParameterInterface
{
    use ParameterTrait;
    use SchemaTrait;

    private string $className;

    private ?object $default = null;

    public function setUp(): void
    {
        $this->className = stdClass::class;
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

    public function withDefault(object $value): ObjectParameterInterface
    {
        if (! $this->type->validate($value)) {
            throw new TypeError(
                message('Default value must be of type %type%')
                    ->withCode('%type%', $this->className)
            );
        }
        $new = clone $this;
        $new->default = $value;

        return $new;
    }

    public function default(): ?object
    {
        return $this->default;
    }

    public function schema(): array
    {
        return [
            'type' => $this->type->primitive(),
            'className' => $this->className(),
            'description' => $this->description(),
            'default' => $this->default() !== null
                ? $this->default()::class
                : null,
        ];
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
