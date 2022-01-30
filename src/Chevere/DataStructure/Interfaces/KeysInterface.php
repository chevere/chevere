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

namespace Chevere\DataStructure\Interfaces;

/**
 * Describes the component in charge of providing an interface exposing object keys.
 */
interface KeysInterface
{
    /**
     * Provides access to the object keys.
     *
     * @return string[]
     */
    public function keys(): array;
}
