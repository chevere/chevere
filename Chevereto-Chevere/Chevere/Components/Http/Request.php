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

namespace Chevere\Components\Http;

use GuzzleHttp\Psr7\Request as GuzzleHttpRequest;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

use Chevere\Components\Globals\Globals;
use Chevere\Components\Http\Traits\RequestTrait;
use Chevere\Contracts\Http\RequestContract;

final class Request extends GuzzleHttpRequest implements RequestContract
{
    use RequestTrait;

    /** @var Globals */
    private $globals;

    /**
     * @param string                               $method  HTTP method
     * @param string|UriInterface                  $uri     URI
     * @param array                                $headers Request headers
     * @param string|null|resource|StreamInterface $body    Request body
     * @param string                               $version Protocol version
     */
    public function __construct(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $version = '1.1'
    ) {
        $this->globals = new Globals($GLOBALS);
        parent::__construct($method, $uri, $headers, $body, $version);
    }
}
