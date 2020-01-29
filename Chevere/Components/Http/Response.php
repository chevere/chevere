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

use DateTime;
use DateTimeZone;
use Chevere\Components\Http\Interfaces\ResponseInterface;

final class Response implements ResponseInterface
{
    private GuzzleResponse $guzzle;

    /**
     * Creates a new instance with a default GuzzleResponse object.
     */
    public function __construct()
    {
        $this->guzzle = new GuzzleResponse(200, $this->getDateHeader());
    }

    public function withGuzzle(GuzzleResponse $guzzle): ResponseInterface
    {
        $new = clone $this;
        $new->guzzle = $guzzle;

        return $new;
    }

    public function guzzle(): GuzzleResponse
    {
        return $this->guzzle;
    }

    public function statusLine(): string
    {
        return sprintf('HTTP/%s %s %s', $this->guzzle->getProtocolVersion(), $this->guzzle->getStatusCode(), $this->guzzle->getReasonPhrase());
    }

    public function headersString(): string
    {
        $headers = [];
        foreach ($this->guzzle->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers[] = "${name}: ${value}";
            }
        }

        return implode("\n", $headers);
    }

    public function content(): string
    {
        return (string) $this->guzzle->getBody();
    }

    public function sendHeaders(): ResponseInterface
    {
        header($this->statusLine(), true, $this->guzzle->getStatusCode());
        foreach ($this->guzzle->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("${name}: ${value}", false);
            }
        }

        return $this;
    }

    public function sendBody(): ResponseInterface
    {
        $stream = $this->guzzle->getBody();
        if ($stream->isSeekable()) {
            $stream->rewind();
        }
        while (!$stream->eof()) {
            echo $stream->read(1024 * 8);
        }

        return $this;
    }

    private function getDateHeader(): array
    {
        $date = new DateTime('now', new DateTimeZone('UTC'));

        return ['Date' => $date->format('D, d M Y H:i:s') . ' GMT'];
    }
}
