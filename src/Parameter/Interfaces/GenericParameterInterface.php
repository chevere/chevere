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
 * Describes the component in charge of defining a parameter of type generic.
 */
interface GenericParameterInterface extends ArrayTypeParameterInterface
{
    /**
     * Asserts the given `$value` is valid, returning the value if so.
     * @phpstan-ignore-next-line
     */
    public function __invoke(iterable $value): iterable;

    public function key(): ParameterInterface;

    public function value(): ParameterInterface;

    public function assertCompatible(self $parameter): void;
}
