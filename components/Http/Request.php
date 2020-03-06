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

use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use GuzzleHttp\Psr7\ServerRequest;

final class Request
{
    private MethodInterface $method;

    private RoutePathInterface $routePath;

    private array $headers = [];

    private string $body = '';

    private string $version = '1.1';

    private array $serverParams = [];

    public function __construct(
        MethodInterface $method,
        RoutePathInterface $routePath, // /path, /path/{wea}, /path/123 -> pero como saber si {wea}<=>123?
        array $headers = [],
        string $body = '',
        string $version = '1.1',
        array $serverParams = []
    ) {
        $this->method = $method;
        $this->routePath = $routePath;
        $this->headers = $headers;
        $this->body = $body;
        $this->version = $version;
        $this->serverParams = $serverParams;
    }

    public function method(): MethodInterface
    {
        return $this->method;
    }

    public function pathUri(): RoutePathInterface
    {
        return $this->routePath;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function version(): string
    {
        return $this->version;
    }

    public function serverParams(): array
    {
        return $this->serverParams;
    }
}
