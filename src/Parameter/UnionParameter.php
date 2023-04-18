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
use Chevere\Parameter\Traits\ParametersAccessTrait;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Type\typeUnion;

final class UnionParameter implements UnionParameterInterface
{
    use ParameterTrait;
    use ArrayParameterTrait;
    use ParameterAssertArrayTypeTrait;
    use ParametersAccessTrait;

    /**
     * @var array<mixed, mixed>
     */
    private array $default = [];

    final public function __construct(
        private ParametersInterface $parameters,
        private ?string $description = null,
    ) {
        $this->setUp(); // @codeCoverageIgnore
        $this->type = $this->type();
        $this->parameters = $parameters;
    }

    public function setUp(): void
    {
        $this->parameters = new Parameters();
    }

    public function withAdded(ParameterInterface ...$parameter): static
    {
        $new = clone $this;
        $new->parameters = $new->parameters
            ->withAddedRequired(...$parameter);

        return $new;
    }

    public function assertCompatible(UnionParameterInterface $parameter): void
    {
        $this->assertArrayType($parameter);
    }

    public function getType(): TypeInterface
    {
        return typeUnion();
    }
}
