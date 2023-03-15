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

namespace Chevere\Parameter\Interfaces;

/**
 * Describes the component in charge of defining a parameter of type union.
 */
interface UnionParameterInterface extends ParameterInterface
{
    /**
     * Return an instance with the specified property.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified property.
     */
    public function withAddedRequired(ParameterInterface ...$parameter): self;

    public function assertCompatible(self $parameter): void;
}
