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

namespace Chevere\ClassMap\Interfaces;

use Chevere\Common\Interfaces\ToArrayInterface;
use Chevere\DataStructure\Interfaces\MappedInterface;
use Chevere\Throwable\Exceptions\ClassNotExistsException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;

/**
 * Describes the component in charge of mapping classes to keys.
 *
 * @extends MappedInterface<string>
 */
interface ClassMapInterface extends MappedInterface, ToArrayInterface
{
    /**
     * Return an instance with the specified className mapping.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified className mapping.
     *
     * @throws ClassNotExistsException
     * @throws OverflowException
     */
    public function withPut(string $className, string $key): self;

    /**
     * Indicates whether the instance maps the given key.
     */
    public function has(string ...$key): bool;

    /**
     * Provides access to the class name mapping.
     *
     * @throws OutOfBoundsException
     */
    public function key(string $className): string;

    /**
     * Provides access to the class map `className => key`
     *
     * @return array<string, string>
     */
    public function toArray(): array;
}
