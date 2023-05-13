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

use Chevere\Attribute\StringAttribute;

/**
 * Describes the component in charge of provide a typed reflection parameter.
 */
interface ReflectionParameterTypedInterface
{
    public function attribute(): StringAttribute;

    public function default(): mixed;

    public function parameter(): ParameterInterface;
}
