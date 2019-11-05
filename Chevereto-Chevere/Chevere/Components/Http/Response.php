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

use DateTime;
use DateTimeZone;

use Chevere\Contracts\Http\ResponseContract;

final class Response implements ResponseContract
{
    /** @var GuzzleResponse */
    private $guzzle;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->guzzle = new GuzzleResponse(200, $this->getDateHeader());
    }

    /**
     * {@inheritdoc}
     */
    public function withGuzzle(GuzzleResponse $guzzle): ResponseContract
    {
        $new = clone $this;
        $new->guzzle = $guzzle;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function guzzle(): GuzzleResponse
    {
        return $this->guzzle;
    }

    /**
     * {@inheritdoc}
     */
    public function statusLine(): string
    {
        return sprintf('HTTP/%s %s %s', $this->guzzle->getProtocolVersion(), $this->guzzle->getStatusCode(), $this->guzzle->getReasonPhrase());
    }

    /**
     * {@inheritdoc}
     */
    public function headersString(): string
    {
        $headers = [];
        foreach ($this->guzzle->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers[] = "$name: $value";
            }
        }
        return implode("\n", $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function content(): string
    {
        return (string) $this->guzzle->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function sendHeaders(): ResponseContract
    {
        header($this->statusLine(), true, $this->guzzle->getStatusCode());
        foreach ($this->guzzle->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function sendBody(): ResponseContract
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
