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

namespace Chevere\Components\Spec\Specs\Traits;

trait SpecsTrait
{
    private string $jsonPath;

    private string $key;

    public function jsonPath(): string
    {
        return $this->jsonPath;
    }

    public function key(): string
    {
        return $this->key;
    }

    abstract public function toArray(): array;
}
