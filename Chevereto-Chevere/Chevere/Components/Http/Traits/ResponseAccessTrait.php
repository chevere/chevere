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

namespace Chevere\Components\Http\Traits;

use Chevere\Contracts\Http\ResponseContract;

trait ResponseAccessTrait
{
    /** @var ResponseContract */
    private $response;

    public function hasResponse(): bool
    {
        return isset($this->response);
    }

    public function response(): ResponseContract
    {
        return $this->response;
    }
}
