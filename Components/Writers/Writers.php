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

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Writers\WriterInterface;
use Chevere\Interfaces\Writers\WritersInterface;
use Laminas\Diactoros\Stream;

final class Writers implements WritersInterface
{
    private WriterInterface $out;

    private WriterInterface $error;

    private WriterInterface $debug;

    private WriterInterface $log;

    public function __construct()
    {
        $stdout = fopen('php://stdout', 'w');
        $stderr = fopen('php://stderr', 'w');
        $errors = [];
        // @codeCoverageIgnoreStart
        if ($stdout === false) {
            $errors[] = 'php://stdout';
        }
        if ($stderr === false) {
            $errors[] = 'php://stderr';
        }
        if ($errors !== []) {
            throw new RuntimeException(
                (new Message('Unable to open %list%'))
                ->code('%list%', implode('; ', $errors))
            );
        }
        // @codeCoverageIgnoreEnd
        $this->out = new StreamWriter(new Stream($stdout), 'w');
        $this->error = new StreamWriter(new Stream($stderr), 'w');
        $this->debug = new NullWriter;
        $this->log = new NullWriter;
    }

    public function withOut(WriterInterface $writer): WritersInterface
    {
        $new = clone $this;
        $new->out = $writer;

        return $new;
    }

    public function withError(WriterInterface $writer): WritersInterface
    {
        $new = clone $this;
        $new->error = $writer;

        return $new;
    }

    public function withDebug(WriterInterface $writer): WritersInterface
    {
        $new = clone $this;
        $new->debug = $writer;

        return $new;
    }

    public function withLog(WriterInterface $writer): WritersInterface
    {
        $new = clone $this;
        $new->log = $writer;

        return $new;
    }

    public function out(): WriterInterface
    {
        return $this->out;
    }

    public function error(): WriterInterface
    {
        return $this->error;
    }

    public function debug(): WriterInterface
    {
        return $this->debug;
    }

    public function log(): WriterInterface
    {
        return $this->log;
    }
}
