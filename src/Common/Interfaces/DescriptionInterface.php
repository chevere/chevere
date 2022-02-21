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
 * Describes the component in charge of providing an interface for its description.
 */
interface DescriptionInterface
{
    /**
     * Provides access to the description.
     */
    public function description(): string;

    /**
     * Defines the description.
     */
    public function getDescription(): string;

    public function withDescription(string $description): static;
}
