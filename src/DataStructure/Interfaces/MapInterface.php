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

namespace Chevere\DataStructure\Interfaces;

/**
 * Describes the component in charge of providing a Map interface.
 */
interface MapInterface extends MappedInterface
{
    public function __construct(mixed ...$value);

    public function withPut(mixed ...$value): self;

    public function has(string ...$keys): bool;

    public function assertHas(string ...$keys): void;

    public function get(string $key): mixed;
}
