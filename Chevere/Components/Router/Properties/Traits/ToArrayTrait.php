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

namespace Chevere\Components\Router\Properties\Traits;

trait ToArrayTrait
{
    /** @var array */
    private array $value;

    public function toArray(): array
    {
        return $this->value;
    }
}
