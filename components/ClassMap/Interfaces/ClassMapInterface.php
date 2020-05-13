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

namespace Chevere\Components\ClassMap\Interfaces;

use Countable;

interface ClassMapInterface extends Countable
{
    public function withPut(string $className, string $string): ClassMapInterface;

    public function has(string $className): bool;

    public function get(string $className): string;

    public function toArray(): array;
}
