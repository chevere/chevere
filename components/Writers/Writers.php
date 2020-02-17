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
    private WriterInterface $out;

    private WriterInterface $error;

    private WriterInterface $debug;

    private WriterInterface $log;

    public function __construct()
    {
        $this->out = new StreamWriter(stream_for(fopen('php://stdout', 'w')));
        $this->error = new StreamWriter(stream_for(fopen('php://stderr', 'w')));
        $this->debug = new NullWriter();
        $this->log = new NullWriter();
    }

    public function withOut(WriterInterface $writter): WritersInterface
    {
        $new = clone $this;
        $new->out = $writter;

        return $new;
    }

    public function out(): WriterInterface
    {
        return $this->out;
    }

    public function withError(WriterInterface $writter): WritersInterface
    {
        $new = clone $this;
        $new->error = $writter;

        return $new;
    }

    public function error(): WriterInterface
    {
        return $this->error;
    }

    public function withDebug(WriterInterface $writter): WritersInterface
    {
        $new = clone $this;
        $new->debug = $writter;

        return $new;
    }

    public function debug(): WriterInterface
    {
        return $this->debug;
    }

    public function withLog(WriterInterface $writter): WritersInterface
    {
        $new = clone $this;
        $new->log = $writter;

        return $new;
    }

    public function log(): WriterInterface
    {
        return $this->log;
    }
}
