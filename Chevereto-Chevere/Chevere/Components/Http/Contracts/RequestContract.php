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

namespace Chevere\Components\Http\Contracts;

use Psr\Http\Message\RequestInterface;
use Chevere\Components\Globals\Contracts\GlobalsContract;
use Chevere\Components\Route\PathUri;

interface RequestContract extends RequestInterface
{
    public function __construct(
        MethodContract $method,
        PathUri $uri,
        array $headers = [],
        $body = null,
        $version = '1.1',
        array $serverParams = []
    );

    public function isXmlHttpRequest(): bool;

    public function protocolString(): string;

    public function globals(): GlobalsContract;
}
