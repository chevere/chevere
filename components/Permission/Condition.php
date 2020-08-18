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

namespace Chevere\Components\Permission;

use Chevere\Components\Description\Traits\DescriptorTrait;
use Chevere\Components\Permission\Traits\IdentifierTrait;
use Chevere\Interfaces\Permission\ConditionInterface;

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

    abstract public function getDefault(): bool;

    final public function value(): bool
    {
        return $this->value;
    }
}
