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

use Chevere\Components\Attribute\Traits\IdentifierTrait;
use Chevere\Components\Description\Traits\DescriptorTrait;
use Chevere\Interfaces\Attribute\ConditionInterface;

/**
 * @codeCoverageIgnore
 */
abstract class Condition implements ConditionInterface
{
    use DescriptorTrait;
    use IdentifierTrait;

    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    final public function value(): bool
    {
        return $this->value;
    }
}
