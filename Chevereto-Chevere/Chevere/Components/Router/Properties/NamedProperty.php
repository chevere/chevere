<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Router\Properties;

use Chevere\Components\Router\Properties\Traits\ToArrayTrait;
use Chevere\Contracts\Router\Properties\NamedPropertyContract;

final class NamedProperty implements NamedPropertyContract
{
    use ToArrayTrait;

    public function __construct(array $named)
    {
        $this->value = $named;
    }

    /**
     * @throws RouterPropertyException if the value doesn't match the property format
     */
    public function assert(): void
    {
        dd($this->value);
    }
}
