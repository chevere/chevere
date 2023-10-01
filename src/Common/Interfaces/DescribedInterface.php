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
 * Describes the component in charge of providing an interface for access to description.
 */
interface DescribedInterface
{
    /**
     * Provides access to the description.
     */
    public function description(): string;

    /**
     * Return an instance with the specified description.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified description.
     */
    public function withDescription(string $description): self;
}
