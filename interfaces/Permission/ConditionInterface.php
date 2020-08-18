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

namespace Chevere\Interfaces\Permission;

use Chevere\Interfaces\Description\DescriptorInterface;

/**
 * Describes the component in charge of defining a conditional.
 */
interface ConditionInterface extends DescriptorInterface
{
    /**
     * Provides access to the default value.
     */
    public function getDefault(): bool;

    /**
     * Provides access to the boolean value.
     */
    public function value(): bool;
}
