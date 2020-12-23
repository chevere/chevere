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

namespace Chevere\Interfaces\DataStructure;

/**
 * Describes the component in charge of defining an immutable map.
 */
interface MapInterface extends MappedInterface
{
    public function __construct(mixed ...$namedArguments);

    public function withPut(string $key, $value): self;

    public function assertHasKey(string ...$key): void;

    public function get(string $key);
}
