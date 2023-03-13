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
 * Describes the component in charge of defining an Array type parameter to be
 * used as a base for other array-like parameters.
 */
interface ArrayTypeInterface extends ParameterInterface
{
    /**
     * Provides access to the default value.
     *
     * @return array<mixed, mixed>
     */
    public function default(): array;

    /**
     * Provides access to the parameters.
     */
    public function parameters(): ParametersInterface;
}
