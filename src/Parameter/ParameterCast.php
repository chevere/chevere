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
use Chevere\Parameter\Interfaces\BoolParameterInterface;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\GenericParameterInterface;
use Chevere\Parameter\Interfaces\IntParameterInterface;
use Chevere\Parameter\Interfaces\NullParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterCastInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;

final class ParameterCast implements ParameterCastInterface
{
    // @phpstan-ignore-next-line
    public function __construct(
        private $argument
    ) {
    }

    public function array(): ArrayParameterInterface
    {
        /** @var ArrayParameterInterface */
        return $this->argument;
    }

    public function bool(): BoolParameterInterface
    {
        /** @var BoolParameterInterface */
        return $this->argument;
    }

    public function float(): FloatParameterInterface
    {
        /** @var FloatParameterInterface */
        return $this->argument;
    }

    public function int(): IntParameterInterface
    {
        /** @var IntParameterInterface */
        return $this->argument;
    }

    public function object(): ObjectParameterInterface
    {
        /** @var ObjectParameterInterface */
        return $this->argument;
    }

    public function null(): NullParameterInterface
    {
        /** @var NullParameterInterface */
        return $this->argument;
    }

    public function union(): UnionParameterInterface
    {
        /** @var UnionParameterInterface */
        return $this->argument;
    }

    public function generic(): GenericParameterInterface
    {
        /** @var GenericParameterInterface */
        return $this->argument;
    }

    public function string(): StringParameterInterface
    {
        /** @var StringParameterInterface */
        return $this->argument;
    }
}
