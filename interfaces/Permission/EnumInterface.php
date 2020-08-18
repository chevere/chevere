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
 * Describes the component in charge of defining a list of accepted values.
 */
interface EnumInterface extends DescriptorInterface
{
    /**
     * Provides access to the default value.
     */
    public function getDefault(): string;

    /**
     * Declares the default accepted values.
     *
     * @return string[]
     */
    public function getAccept(): array;

    /**
     * Returns the enum value.
     */
    public function value(): string;
}
