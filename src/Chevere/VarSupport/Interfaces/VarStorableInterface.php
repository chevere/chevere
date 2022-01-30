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

namespace Chevere\VarSupport\Interfaces;

use Chevere\VarSupport\Exceptions\VarStorableException;

/**
 * Describes the component in charge of handling storable variables.
 */
interface VarStorableInterface
{
    /**
     * @throws VarStorableException if `$var` is or contains a resource.
     */
    public function __construct($var);

    /**
     * Provides access to passed `$var`.
     */
    public function var(): mixed;

    /**
     * Shorthand for `\var_export($var)`.
     */
    public function toExport(): string;

    /**
     * Shorthand for `\serialize($var)`.
     */
    public function toSerialize(): string;
}
