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

use Chevere\Components\Http\GuzzleResponse;

interface ResponseInterface
{
    /**
     * Return an instance with the specified GuzzleResponse.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified GuzzleResponse.
     */
    public function withGuzzle(GuzzleResponse $guzzle): ResponseInterface;

    /**
     * Provides access to the GuzzleResponse instance.
     */
    public function guzzle(): GuzzleResponse;

    /**
     * Returns a single line representation of the HTTP response status.
     *
     * @return string The HTTP response status like: HTTP/1.1 200 OK
     */
    public function statusLine(): string;

    /**
     * Returns a the HTTP response headers, line-by-line.
     */
    public function headersString(): string;

    /**
     * Returns the HTTP response body.
     */
    public function content(): string;

    /**
     * Send the HTTP response headers.
     */
    public function sendHeaders(): ResponseInterface;

    /**
     * Send the HTTP response body.
     */
    public function sendBody(): ResponseInterface;
}
