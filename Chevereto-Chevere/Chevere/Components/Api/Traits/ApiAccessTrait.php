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

namespace Chevere\Components\Api\Traits;

use Chevere\Contracts\Api\ApiContract;

trait ApiAccessTrait
{
    /** @var ApiContract */
    private $api;

    public function hasApi(): bool
    {
        return isset($this->api);
    }

    public function api(): ApiContract
    {
        return $this->api;
    }
}
