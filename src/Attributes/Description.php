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

namespace Chevere\Attributes;

use Attribute;
use Stringable;

#[Attribute]
class Description implements Stringable
{
    public function __construct(
        public string $description = '',
    ) {
    }

    public function __toString(): string
    {
        return $this->description;
    }
}
