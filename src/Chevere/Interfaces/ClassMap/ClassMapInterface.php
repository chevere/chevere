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

namespace Chevere\Interfaces\ClassMap;

use Chevere\Exceptions\ClassMap\ClassNotExistsException;
use Chevere\Exceptions\ClassMap\ClassNotMappedException;
use Chevere\Exceptions\ClassMap\StringMappedException;
use Chevere\Interfaces\DataStructure\MappedInterface;
use Generator;

/**
 * Describes the component in charge of mapping classes to keys.
 */
interface ClassMapInterface extends MappedInterface
{
    /**
     * Return an instance with the specified className mapping.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified className mapping.
     *
     * @throws ClassNotExistsException
     * @throws StringMappedException
     */
    public function withPut(string $className, string $key): self;

    /**
     * Indicates whether the instance is mapping the given class name.
     */
    public function has(string $className): bool;

    /**
     * Indicates whether the instance maps the given key.
     */
    public function hasKey(string $key): bool;

    /**
     * Provides access to the class name mapping.
     *
     * @throws ClassNotMappedException
     */
    public function key(string $className): string;

    /**
     * Provides access to the class name mapped by key.
     *
     * @throws ClassNotMappedException
     */
    public function className(string $key): string;

    /**
     * Provides a generator with `className => key`
     *
     * @return Generator<string, string>
     */
    public function getGenerator(): Generator;

    /**
     * Provides access to the class map `className => key`
     *
     * @return array<string, string>
     */
    public function toArray(): array;
}
