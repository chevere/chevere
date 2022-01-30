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

namespace Chevere\Spec\Interfaces;

use Chevere\Common\Interfaces\ToArrayInterface;

/**
 * Describes the component in charge of defining an ambiguous spec.
 */
interface SpecInterface extends ToArrayInterface
{
    /**
     * Provides access to the json path.
     */
    public function jsonPath(): string;

    /**
     * Provides access to the key.
     */
    public function key(): string;
}
