<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
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
use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Globals\Interfaces\GlobalsInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Route\PathUri;
use Psr\Http\Message\StreamInterface;

final class Request extends GuzzleHttpServerRequest implements RequestInterface
{
    use RequestTrait;

    /**
     * @param MethodInterface                       $method       HTTP method
     * @param PathUri                              $uri          URI
     * @param array                                $headers      Request headers
     * @param string|resource|StreamInterface\null $body         Request body
     * @param string                               $version      Protocol version
     * @param array                                $serverParams Typically the $_SERVER superglobal
     */
    public function __construct(
        MethodInterface $method,
        PathUri $uri,
        array $headers = [],
        $body = null,
        $version = '1.1',
        array $serverParams = []
    ) {
        parent::__construct(
            $method::name(),
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

    public function globals(): GlobalsInterface
    {
        return $this->globals;
    }

    /**
     * Return a ServerRequest populated with superglobals.
     */
    public static function fromGlobals(): RequestInterface
    {
        $globals = new Globals($GLOBALS);
        $method = isset($globals->server()['REQUEST_METHOD'])
            ? $globals->server()['REQUEST_METHOD']
            : 'GET';
        $uri = self::getUriFromGlobals();
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
            ->withUploadedFiles(self::normalizeFiles($globals->files()));
    }
}
