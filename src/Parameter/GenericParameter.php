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
use Chevere\Parameter\Interfaces\TypeInterface;
use Chevere\Parameter\Traits\ArrayParameterTrait;
use Chevere\Parameter\Traits\ParameterTrait;

final class GenericParameter implements GenericParameterInterface
{
    use ParameterTrait;
    use ArrayParameterTrait;

    /**
     * @var array<mixed, mixed>
     */
    private ?array $default = null;

    final public function __construct(
        private ParameterInterface $value,
        private ParameterInterface $key,
        private string $description = ''
    ) {
        $this->setUp(); // @codeCoverageIgnore
        $this->type = $this->type();
        $this->parameters = new Parameters(
            K: $this->key,
            V: $this->value
        );
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function __invoke(iterable $value): iterable
    {
        return assertGeneric($this, $value);
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

    public function typeSchema(): string
    {
        return $this->type->primitive();
    }

    private function getType(): TypeInterface
    {
        return new Type(Type::GENERIC);
    }
}
