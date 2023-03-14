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
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Traits\ArrayParameterTrait;
use Chevere\Parameter\Traits\ParameterTrait;

final class GenericParameter implements GenericParameterInterface
{
    use ParameterTrait;
    use ArrayParameterTrait;

    /**
     * @var array<mixed, mixed>
     */
    private array $default = [];

    private ParametersInterface $parameters;

    final public function __construct(
        private ParameterInterface $value,
        private ParameterInterface $key,
        private string $description = ''
    ) {
        $this->setUp();
        $this->type = $this->type();
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

    public function parameters(): ParametersInterface
    {
        return $this->parameters ??= new Generics($this);
    }
}
