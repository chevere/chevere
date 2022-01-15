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

namespace Chevere\Interfaces\Parameter;

/**
 * Describes the component in charge of defining a parameter providing default value.
 */
interface ParameterDefaultInterface extends ParameterInterface
{
    /**
     * Provides access to the default value.
     */
    public function default();
}
