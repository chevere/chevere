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

namespace Chevere\VariableSupport\Interfaces;

/**
 * Describes the component in charge of handling storable variable.
 */
interface StorableVariableInterface
{
    public function __construct(mixed $variable);

    /**
     * Provides access to passed `$variable`.
     */
    public function variable(): mixed;

    /**
     * Shorthand for `\var_export($variable)`.
     */
    public function toExport(): string;

    /**
     * Shorthand for `\serialize($variable)`.
     */
    public function toSerialize(): string;
}
