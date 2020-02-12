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

namespace Chevere\Components\Writers;

use Chevere\Components\Writers\Interfaces\StreamWriterInterface;
use Chevere\Components\Writers\Interfaces\WritersInterface;
use function GuzzleHttp\Psr7\stream_for;

final class Writers implements WritersInterface
{
    private StreamWriterInterface $error;

    private StreamWriterInterface $out;

    private StreamWriterInterface $debug;

    private StreamWriterInterface $log;

    public function __construct()
    {
        // $this->error = new StreamWriter(stream_for(fopen('php://stderr', 'w')));
        // $this->out = new StreamWriter(stream_for(fopen(__DIR__ . '/out', 'w')));
        $this->error = new SilentStreamWriter();
        $this->out = new SilentStreamWriter();
        $this->debug = new SilentStreamWriter();
        $this->log = new SilentStreamWriter();
        $this->out = new StreamWriter(stream_for(fopen('php://stdout', 'w')));
    }

    public function withError(StreamWriterInterface $streamWritter): WritersInterface
    {
        $new = clone $this;
        $new->error = $streamWritter;

        return $new;
    }

    public function error(): StreamWriterInterface
    {
        return $this->error;
    }

    public function withOut(StreamWriterInterface $streamWritter): WritersInterface
    {
        $new = clone $this;
        $new->out = $streamWritter;

        return $new;
    }

    public function out(): StreamWriterInterface
    {
        return $this->out;
    }

    public function withDebug(StreamWriterInterface $streamWritter): WritersInterface
    {
        $new = clone $this;
        $new->debug = $streamWritter;

        return $new;
    }

    public function debug(): StreamWriterInterface
    {
        return $this->debug;
    }

    public function withLog(StreamWriterInterface $streamWritter): WritersInterface
    {
        $new = clone $this;
        $new->log = $streamWritter;

        return $new;
    }

    public function log(): StreamWriterInterface
    {
        return $this->log;
    }
}
