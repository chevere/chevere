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
use Chevere\Contracts\Router\Properties\RoutesPropertyContract;

final class RoutesProperty implements RoutesPropertyContract
{
    use ToArrayTrait;

    /**
     * @throws RouterPropertyException if the value doesn't match the property format
     */
    public function __construct(array $routes)
    {
        $this->value = $routes;
    }
}
