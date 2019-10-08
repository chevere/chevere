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

namespace Chevere\Route\Traits;

use Chevere\Contracts\Route\RouteContract;

trait RouteAccessTrait
{
    /** @var RouteContract */
    private $route;

    public function hasRoute(): bool
    {
        return isset($this->route);
    }

    public function route(): RouteContract
    {
        return $this->route;
    }
}
