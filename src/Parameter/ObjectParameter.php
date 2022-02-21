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
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Type\typeObject;
use stdClass;

final class ObjectParameter implements ObjectParameterInterface
{
    use ParameterTrait;

    private string $className;
    
    public function setUp(): void
    {
        $this->className = stdClass::class;
    }

    public function getType(): TypeInterface
    {
        return typeObject($this->className);
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

    public function default(): mixed
    {
        return null;
    }
}
