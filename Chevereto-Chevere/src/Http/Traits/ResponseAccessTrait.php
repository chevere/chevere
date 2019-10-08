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

namespace Chevere\Http\Traits;

use Chevere\Contracts\Http\ResponseContract;

trait ResponseAccessTrait
{
    /** @var ResponseContract */
    private $response;

    public function response(): ResponseContract
    {
        return $this->response;
    }
}
