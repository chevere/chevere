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

namespace Chevere\Interfaces\Spec;

interface SpecIndexCacheInterface
{
    public function has(string $routeName): bool;

    public function get(string $routeName): SpecMethodsInterface;

    public function put(SpecIndexInterface $specIndex): void;

    public function puts(): array;
}
