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

namespace Chevere\Parameter\Interfaces;

/**
 * Describes the component in charge of casting a parameter.
 */
interface ParameterCastInterface
{
    public function array(): ArrayParameterInterface;

    public function bool(): BoolParameterInterface;

    public function float(): FloatParameterInterface;

    public function int(): IntParameterInterface;

    public function object(): ObjectParameterInterface;

    public function null(): NullParameterInterface;

    public function union(): UnionParameterInterface;

    public function generic(): GenericParameterInterface;

    public function string(): StringParameterInterface;
}
