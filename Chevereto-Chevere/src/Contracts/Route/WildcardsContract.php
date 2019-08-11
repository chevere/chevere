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

namespace Chevere\Contracts\Route;

interface WildcardsContract
{
    public function __construct(string $path);

    public function set(): string;

    public function matches(): array;

    public function toArray(): array;

    public function keyPowerSet(): array;
}
