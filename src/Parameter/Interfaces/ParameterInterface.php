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

use Chevere\Common\Interfaces\DescribedInterface;
use Chevere\Type\Interfaces\TypeInterface;

/**
 * Describes the component in charge of defining a parameter.
 * @method void assertCompatible(self $parameter) Asserts parameter compatibility against the provided `$parameter`.
 */
interface ParameterInterface extends DescribedInterface
{
    /**
     * This method runs before the `__construct` method.
     */
    public function setUp(): void;

    /**
     * Provides access to the type instance.
     */
    public function type(): TypeInterface;

    /**
     * Provides access to the default value.
     */
    public function default(): mixed;

    /**
     * @return array<string, mixed>
     */
    public function schema(): array;
}
