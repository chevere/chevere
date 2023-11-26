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

use Chevere\Parameter\Interfaces\NullParameterInterface;
use Chevere\Parameter\Interfaces\TypeInterface;
use Chevere\Parameter\Traits\ParameterDefaultNullTrait;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Parameter\Traits\SchemaTrait;

final class NullParameter implements NullParameterInterface
{
    use ParameterTrait;
    use ParameterDefaultNullTrait;
    use SchemaTrait;

    public function __invoke(mixed $value): mixed
    {
        return assertNull($this, $value);
    }

    /**
     * @codeCoverageIgnore
     */
    public function assertCompatible(NullParameterInterface $parameter): void
    {
    }

    private function getType(): TypeInterface
    {
        return new Type(Type::NULL);
    }
}
