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

use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Traits\ArrayParameterTrait;
use Chevere\Parameter\Traits\ArrayTypeParameterTrait;
use Chevere\Parameter\Traits\ParameterAssertArrayTypeTrait;
use Chevere\Parameter\Traits\ParameterTrait;

final class ArrayParameter implements ArrayParameterInterface
{
    use ArrayParameterTrait;
    use ArrayTypeParameterTrait;
    use ParameterAssertArrayTypeTrait;
    use ParameterTrait;

    /**
     * @var array<mixed, mixed>
     */
    private ?array $default = null;

    public function setUp(): void
    {
        $this->parameters = new Parameters();
    }

    public function withRequired(ParameterInterface ...$parameter): static
    {
        $new = clone $this;
        $new->put('withRequired', ...$parameter);

        return $new;
    }

    public function withOptional(ParameterInterface ...$parameter): static
    {
        $new = clone $this;
        $new->put('withOptional', ...$parameter);

        return $new;
    }

    public function assertCompatible(ArrayParameterInterface $parameter): void
    {
        $this->assertArrayType($parameter);
    }
}
