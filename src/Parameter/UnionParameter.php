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

use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;
use Chevere\Parameter\Traits\ArrayParameterTrait;
use Chevere\Parameter\Traits\ParameterAssertArrayTypeTrait;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Type\typeUnion;

final class UnionParameter implements UnionParameterInterface
{
    use ParameterTrait;
    use ArrayParameterTrait;
    use ParameterAssertArrayTypeTrait;

    /**
     * @var array<mixed, mixed>|null
     */
    private ?array $default = null;

    final public function __construct(
        private ParametersInterface $parameters,
        private string $description = '',
    ) {
        $this->type = $this->type();
        $this->parameters = $parameters;
    }

    public function withAdded(ParameterInterface ...$parameter): static
    {
        $new = clone $this;
        foreach ($parameter as $name => $item) {
            $name = strval($name);
            $new->parameters = $new->parameters
                ->withRequired($name, $item);
        }

        return $new;
    }

    public function assertCompatible(UnionParameterInterface $parameter): void
    {
        $this->assertArrayType($parameter);
    }

    public function typeSchema(): string
    {
        return $this->type->primitive();
    }

    private function getType(): TypeInterface
    {
        return typeUnion();
    }
}
