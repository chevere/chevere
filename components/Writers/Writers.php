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

use Chevere\Components\Writers\Interfaces\WriterInterface;
use Chevere\Components\Writers\Interfaces\WritersInterface;
use function GuzzleHttp\Psr7\stream_for;

final class Writers implements WritersInterface
{
    private WriterInterface $error;

    private WriterInterface $out;

    private WriterInterface $debug;

    private WriterInterface $log;

    public function __construct()
    {
        // $this->error = new StreamWriter(stream_for(fopen('php://stderr', 'w')));
        // $this->out = new StreamWriter(stream_for(fopen(__DIR__ . '/out.html', 'w')));
        $this->error = new SilentWriter();
        // $this->out = new SilentStreamWriter();
        $this->debug = new SilentWriter();
        $this->log = new SilentWriter();
        $this->out = new StreamWriter(stream_for(fopen('php://stdout', 'w')));
    }

    public function withError(WriterInterface $streamWritter): WritersInterface
    {
        $new = clone $this;
        $new->error = $streamWritter;

        return $new;
    }

    public function error(): WriterInterface
    {
        return $this->error;
    }

    public function withOut(WriterInterface $streamWritter): WritersInterface
    {
        $new = clone $this;
        $new->out = $streamWritter;

        return $new;
    }

    public function out(): WriterInterface
    {
        return $this->out;
    }

    public function withDebug(WriterInterface $streamWritter): WritersInterface
    {
        $new = clone $this;
        $new->debug = $streamWritter;

        return $new;
    }

    public function debug(): WriterInterface
    {
        return $this->debug;
    }

    public function withLog(WriterInterface $streamWritter): WritersInterface
    {
        $new = clone $this;
        $new->log = $streamWritter;

        return $new;
    }

    public function log(): WriterInterface
    {
        return $this->log;
    }
}
