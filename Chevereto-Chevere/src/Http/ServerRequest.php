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
use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\ServerRequest as GuzzleHttpServerRequest;

final class ServerRequest extends GuzzleHttpServerRequest implements RequestContract
{
    use RequestTrait;

    /** @var Globals */
    private $globals;

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
        $this->serverParams = $serverParams;

        parent::__construct($method, $uri, $headers, $body, $version);
    }

    /**
     * Return a ServerRequest populated with superglobals.
     *
     * @return RequestContract
     */
    public static function fromGlobals(): RequestContract
    {

        $method = isset($this->globals->server()['REQUEST_METHOD'])
            ? $this->globals->server()['REQUEST_METHOD']
            : 'GET';
        $headers = getallheaders() ?: [];
        $uri = static::getUriFromGlobals();
        $body = new CachingStream(new LazyOpenStream('php://input', 'r+'));
        $protocol = isset($this->globals->server()['SERVER_PROTOCOL'])
            ? str_replace('HTTP/', '', $this->globals->server()['SERVER_PROTOCOL'])
            : '1.1';

        $serverRequest = new static($method, $uri, $headers, $body, $protocol, $this->globals->server());

        return $serverRequest
            ->withCookieParams($this->globals->cookie())
            ->withQueryParams($this->globals->get())
            ->withParsedBody($this->globals->post())
            ->withUploadedFiles(static::normalizeFiles($this->globals->files()));
    }

    public function getGlobals(): Globals
    {
        return $this->globals;
    }
}
