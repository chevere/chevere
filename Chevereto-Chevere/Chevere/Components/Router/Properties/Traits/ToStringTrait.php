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

namespace Chevere\Components\Router\Properties\Traits;

trait ToStringTrait
{
    /** @var string */
    private $value;

    public function toString(): string
    {
        return $this->value;
    }
}
