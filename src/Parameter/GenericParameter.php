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

use Chevere\Parameter\Interfaces\GenericParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Traits\ArrayParameterTrait;
use Chevere\Parameter\Traits\ParameterTrait;

final class GenericParameter implements GenericParameterInterface
{
    use ParameterTrait;
    use ArrayParameterTrait;

    private ParameterInterface $key;

    private ParameterInterface $value;

    /**
     * @var array<mixed, mixed>
     */
    private array $default = [];

    public function setUp(): void
    {
        $this->key = integerParameter();
        $this->value = stringParameter();
    }

    public function withKey(ParameterInterface $key): GenericParameterInterface
    {
        $new = clone $this;
        $new->key = $key;

        return $new;
    }

    public function withValue(ParameterInterface $parameter): GenericParameterInterface
    {
        $new = clone $this;
        $new->value = $parameter;

        return $new;
    }

    public function key(): ParameterInterface
    {
        return $this->key;
    }

    public function value(): ParameterInterface
    {
        return $this->value;
    }

    public function assertCompatible(GenericParameterInterface $parameter): void
    {
        $this->key->assertCompatible($parameter->key());
        $this->value->assertCompatible($parameter->value());
    }
}
