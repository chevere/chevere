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

namespace Chevere\Components\Parameter;

use Chevere\Components\Parameter\Traits\ParameterTrait;
use function Chevere\Components\Type\typeObject;
use Chevere\Interfaces\Parameter\ObjectParameterInterface;
use Chevere\Interfaces\Type\TypeInterface;
use stdClass;

final class ObjectParameter implements ObjectParameterInterface
{
    use ParameterTrait;

    private string $className;

    public function __construct(
        private string $description = ''
    ) {
        $this->className = stdClass::class;
        $this->setUp();
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
}
