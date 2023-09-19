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
use Chevere\Parameter\Interfaces\BooleanParameterInterface;
use Chevere\Parameter\Interfaces\CastParameterInterface;
use Chevere\Parameter\Interfaces\FileParameterInterface;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\GenericParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\NullParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Interfaces\UnionParameterInterface;

final class CastParameter implements CastParameterInterface
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

    public function boolean(): BooleanParameterInterface
    {
        /** @var BooleanParameterInterface */
        return $this->argument;
    }

    public function file(): FileParameterInterface
    {
        /** @var FileParameterInterface */
        return $this->argument;
    }

    public function float(): FloatParameterInterface
    {
        /** @var FloatParameterInterface */
        return $this->argument;
    }

    public function integer(): IntegerParameterInterface
    {
        /** @var IntegerParameterInterface */
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
