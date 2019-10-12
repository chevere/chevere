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
use Chevere\Globals\Globals;
use Chevere\Http\Traits\GlobalsTrait;
use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\ServerRequest as GuzzleHttpServerRequest;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;

final class ServerRequest extends GuzzleHttpServerRequest implements RequestContract
{
    use RequestTrait;
    use GlobalsTrait;

    /**
     * @param string                               $method       HTTP method
     * @param string|UriInterface                  $uri          URI
     * @param array                                $headers      Request headers
     * @param string|null|resource|StreamInterface $body         Request body
     * @param string                               $version      Protocol version
     * @param array                                $serverParams Typically the $_SERVER superglobal
     */
    public function __construct(
        $method,
        $uri,
        array $headers = [],
        $body = null,
        $version = '1.1',
        array $serverParams = []
    ) {
        $this->globals = new Globals($GLOBALS);

        parent::__construct($method, $uri, $headers, $body, $version, $serverParams ?? $this->globals->server());
    }

    /**
     * Return a ServerRequest populated with superglobals.
     *
     * @return RequestContract
     */
    public static function fromGlobals(): RequestContract
    {
        $globals = new Globals($GLOBALS);
        $method = isset($globals->server()['REQUEST_METHOD'])
            ? $globals->server()['REQUEST_METHOD']
            : 'GET';
        $headers = getallheaders() ?: [];
        $uri = static::getUriFromGlobals();
        $body = new CachingStream(new LazyOpenStream('php://input', 'r+'));
        $protocol = isset($globals->server()['SERVER_PROTOCOL'])
            ? str_replace('HTTP/', '', $globals->server()['SERVER_PROTOCOL'])
            : '1.1';

        $serverRequest = new static($method, $uri, $headers, $body, $protocol, $globals->server());
        $serverRequest->globals = $globals;
        return $serverRequest
            ->withCookieParams($globals->cookie())
            ->withQueryParams($globals->get())
            ->withParsedBody($globals->post())
            ->withUploadedFiles(static::normalizeFiles($globals->files()));
    }
}
