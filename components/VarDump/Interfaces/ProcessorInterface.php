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

namespace Chevere\Components\VarDump\Interfaces;

interface ProcessorInterface
{
    const MAX_DEPTH = 5;

    /**
     * Provides access to the instance info.
     * The information about the variable like `size=1` or `length=6`
     */
    public function info(): string;

    /**
     * Provides access to the instance value.
     * The dumped variable value.
     */
    public function value(): string;

    /**
     * Provides access to the instance type.
     * The information about the variable type like `array` or `object`
     */
    public function type(): string;
}
