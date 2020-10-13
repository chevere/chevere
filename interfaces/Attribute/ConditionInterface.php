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

namespace Chevere\Interfaces\Attribute;

use Chevere\Interfaces\Description\GetDescriptionInterface;
use Chevere\Interfaces\Identifier\GetIdentifierInterface;

/**
 * Describes the component in charge of defining a conditional.
 */
interface ConditionInterface extends GetDescriptionInterface, GetIdentifierInterface
{
    /**
     * Provides access to the boolean value.
     */
    public function value(): bool;
}
