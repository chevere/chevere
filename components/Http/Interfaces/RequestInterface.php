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

namespace Chevere\Components\Http\Interfaces;

use Psr\Http\Message\RequestInterface as BaseInterface;
use Chevere\Components\Globals\Interfaces\GlobalsInterface;
use Chevere\Components\Route\PathUri;

interface RequestInterface extends BaseInterface
{
    public function __construct(
        MethodInterface $method,
        PathUri $uri,
        array $headers = [],
        $body = null,
        $version = '1.1',
        array $serverParams = []
    );

    public function isXmlHttpRequest(): bool;

    public function protocolString(): string;

    public function globals(): GlobalsInterface;
}
