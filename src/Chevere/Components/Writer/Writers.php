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

namespace Chevere\Components\Writer;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Writer\WriterInterface;
use Chevere\Interfaces\Writer\WritersInterface;
use Laminas\Diactoros\Exception\InvalidArgumentException;

final class Writers implements WritersInterface
{
    private WriterInterface $out;

    private WriterInterface $error;

    private WriterInterface $debug;

    private WriterInterface $log;

    public function __construct()
    {
        try {
            $this->out = new StreamWriter(streamForString(''));
            $this->error = new StreamWriter(streamForString(''));
        }
        // @codeCoverageIgnoreStart
        catch (InvalidArgumentException $e) {
            throw new LogicException(
                previous: $e,
                message: new Message('Unable to create default streams'),
            );
        }
        // @codeCoverageIgnoreEnd
        $this->debug = new NullWriter();
        $this->log = new NullWriter();
    }

    public function with(WriterInterface $writer): WritersInterface
    {
        $new = clone $this;
        $new->out = $writer;
        $new->error = $writer;
        $new->debug = $writer;
        $new->log = $writer;

        return $new;
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
