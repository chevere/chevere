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
 * Describes the component in charge of defining a parameter of type object (typed).
 */
interface ObjectParameterInterface extends ParameterInterface
{
    public function className(): string;

    public function withClassName(string $className): self;

    public function withDefault(object $default): self;

    public function assertCompatible(self $parameter): void;
}
