<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Contracts\HttpFoundation;

use ArrayIterator;

interface MethodsContract
{
    public function add(MethodContract $method): void;

    public function has(string $method): bool;

    public function get(string $method): string;

    public function getIterator(): ArrayIterator;
}
