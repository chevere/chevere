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

        return $serverRequest
            ->withGlobals($globals)
            ->withCookieParams($globals->cookie())
            ->withQueryParams($globals->get())
            ->withParsedBody($globals->post())
            ->withUploadedFiles(static::normalizeFiles($globals->files()));
    }

    public function withGlobals(Globals $globals): RequestContract
    {
        $new = clone $this;
        $new->globals = $globals;

        return $new;
    }

    public function hasGlobals(): bool
    {
        return isset($this->globals);
    }

    public function getGlobals(): Globals
    {
        return $this->globals;
    }
}
