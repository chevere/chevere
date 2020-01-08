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
use Chevere\Contracts\Globals\GlobalsContract;

interface RequestContract extends RequestInterface
{
    public function isXmlHttpRequest(): bool;

    public function protocolString(): string;

    public function globals(): GlobalsContract;
}
