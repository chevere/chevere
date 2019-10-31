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

namespace Chevere\Components\Router\Traits;

use Chevere\Contracts\App\ServicesContract;

trait ServicesAccessTrait
{
    /** @var ServicesContract */
    private $services;

    public function hasServices(): bool
    {
        return isset($this->services);
    }

    public function services(): ServicesContract
    {
        return $this->services;
    }
}
