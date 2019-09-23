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

namespace Chevere\Http;

use Chevere\Contracts\Http\RequestContract;
use Chevere\Http\Traits\RequestTrait;
use GuzzleHttp\Psr7\Request as GuzzleHttpRequest;

// FIXME: Client?
final class Request extends GuzzleHttpRequest implements RequestContract
{
    use RequestTrait;
}
