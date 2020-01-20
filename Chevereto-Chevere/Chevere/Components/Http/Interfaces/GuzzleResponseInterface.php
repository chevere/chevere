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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

interface GuzzleResponseInterface
{
    /**
     * Return an instance with the specified StreamInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified StreamInterface.
     */
    public function withJsonApi(StreamInterface $jsonApi): ResponseInterface;

    /**
     * Return an instance with Json Api headers.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains Json Api headers.
     */
    public function withJsonApiHeaders(): ResponseInterface;
}
