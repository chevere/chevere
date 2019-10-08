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

namespace Chevere\Router\Traits;

use Chevere\Contracts\Router\RouterContract;

trait RouterAccessTrait
{
    /** @var RouterContract */
    private $router;

    public function hasRouter(): bool
    {
        return isset($this->router);
    }

    public function router(): RouterContract
    {
        return $this->router;
    }
}
