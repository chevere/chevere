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

namespace Chevere\Interfaces\Identifier;

/**
 * Describes the component in charge of providing an interface for identifier.
 */
interface IdentifierInterface
{
    /**
     * Provides access to the identifier.
     */
    public function identifier(): string;
}
