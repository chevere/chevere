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

namespace Chevere\Pluggable\Interfaces;

/**
 * Describes the component in charge of defining a plug.
 */
interface PlugInterface
{
    /**
     * Plugs at anchor.
     */
    public function anchor(): string;

    /**
     * Plugs at class name.
     */
    public function at(): string;

    /**
     * Plugs at priority.
     */
    public function priority(): int;
}
