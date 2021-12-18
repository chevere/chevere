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

namespace Chevere\Interfaces\Dependent;

use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\DataStructure\MappedInterface;
use Iterator;

/**
 * Describes the component in charge of defining dependencies.
 */
interface DependenciesInterface extends MappedInterface
{
    public function __construct(string ...$dependencies);

    public function withPut(string ...$dependencies): self;

    public function withMerge(self $dependencies): self;

    /**
     * Indicates whether the instance declares a dependency for the given key.
     */
    public function hasKey(string $key): bool;

    /**
     * Provides access to the dependency class name.
     *
     * @throws OutOfBoundsException
     */
    public function key(string $key): string;

    /**
     * @return Iterator<string, string> Name to dependency class name
     */
    public function getIterator(): Iterator;
}
