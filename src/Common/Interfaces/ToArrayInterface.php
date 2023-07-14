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

namespace Chevere\Common\Interfaces;

/**
 * Describes the component that implements `toArray()` method.
 */
interface ToArrayInterface
{
    /**
     * Returns an array, representing the object itself or some of its data/properties.
     * @phpstan-ignore-next-line
     */
    public function toArray(): array;
}
