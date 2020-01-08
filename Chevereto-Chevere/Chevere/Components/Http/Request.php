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

namespace Chevere\Components\Http;

use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\ServerRequest as GuzzleHttpServerRequest;
use Chevere\Components\Globals\Globals;
use Chevere\Components\Http\Traits\RequestTrait;
use Chevere\Components\Http\Contracts\RequestContract;
use Chevere\Components\Globals\Contracts\GlobalsContract;
use Chevere\Components\Http\Contracts\MethodContract;
use Chevere\Components\Route\PathUri;

final class Request extends GuzzleHttpServerRequest implements RequestContract
{
    use RequestTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        MethodContract $method,
        PathUri $uri,
        array $headers = [],
        $body = null,
        $version = '1.1',
        array $serverParams = []
    ) {
        parent::__construct(
            $method->toString(),
            $uri->toString(),
            $headers,
            $body,
            $version,
            $serverParams
        );
        $globals =
            [
                'server' => $this->serverParams ?? [],
                'get' => $this->queryParams ?? [],
                'post' => $this->parsedBody ?? [], // null,array,object
                'files' => $this->uploadedFiles ?? [],
                'cookie' => $this->cookieParams ?? [],
                'session' => $_SESSION ?? [],
            ];
        $globals['argc'] = $globals['server']['argc'] ?? 0;
        $globals['argv'] = $globals['server']['argv'] ?? [];
        $this->globals = new Globals($globals);
    }

    /**
     * {@inheritdoc}
     */
    public function globals(): GlobalsContract
    {
        return $this->globals;
    }

    /**
     * Return a ServerRequest populated with superglobals.
     */
    public static function fromGlobals(): RequestContract
    {
        $globals = new Globals($GLOBALS);
        $method = isset($globals->server()['REQUEST_METHOD'])
            ? $globals->server()['REQUEST_METHOD']
            : 'GET';
        $uri = static::getUriFromGlobals();
        $path = '/' . ltrim($uri->getPath(), '/');
        $body = new CachingStream(new LazyOpenStream('php://input', 'r+'));
        $protocol = isset($globals->server()['SERVER_PROTOCOL'])
            ? str_replace('HTTP/', '', $globals->server()['SERVER_PROTOCOL'])
            : '1.1';
        $serverRequest = new self(
            new Method($method),
            new PathUri($path),
            getallheaders() ?: [],
            $body,
            $protocol,
            $globals->server()
        );

        return $serverRequest
            ->withCookieParams($globals->cookie())
            ->withQueryParams($globals->get())
            ->withParsedBody($globals->post())
            ->withUploadedFiles(static::normalizeFiles($globals->files()));
    }
}
