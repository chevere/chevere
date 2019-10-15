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

namespace Chevere\Contracts\Http;

use Psr\Http\Message\RequestInterface;

use Chevere\Components\Globals\Globals;

interface RequestContract extends RequestInterface
{
    public function isXmlHttpRequest(): bool;

    public function protocolString(): string;

    public function getGlobals(): Globals;
}
