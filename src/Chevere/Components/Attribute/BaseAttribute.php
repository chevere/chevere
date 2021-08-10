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

namespace Chevere\Components\Attribute;

use Attribute;
use Chevere\Interfaces\Attribute\AttributeInterface;

#[Attribute]
abstract class BaseAttribute implements AttributeInterface
{
    public function __construct(protected string $attribute)
    {
    }

    public function attribute(): string
    {
        return $this->attribute;
    }
}
