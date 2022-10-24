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

namespace Chevere\Writer;

use function Chevere\Message\message;
use Chevere\Throwable\Exceptions\LogicException;
use Chevere\Writer\Interfaces\WriterInterface;
use Chevere\Writer\Interfaces\WritersInterface;
use Laminas\Diactoros\Exception\InvalidArgumentException;

final class Writers implements WritersInterface
{
    private WriterInterface $output;

    private WriterInterface $error;

    private WriterInterface $debug;

    private WriterInterface $log;

    public function __construct()
    {
        try {
            $this->output = new StreamWriter(streamTemp(''));
            $this->error = new StreamWriter(streamTemp(''));
        }
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        catch (InvalidArgumentException $e) {
            throw new LogicException(
                previous: $e,
                message: message('Unable to create default streams'),
            );
        }
        // @codeCoverageIgnoreEnd
        $this->debug = new NullWriter();
        $this->log = new NullWriter();
    }

    public function with(WriterInterface $writer): WritersInterface
    {
        $new = clone $this;
        $new->output = $writer;
        $new->error = $writer;
        $new->debug = $writer;
        $new->log = $writer;

        return $new;
    }

    public function withOutput(WriterInterface $writer): WritersInterface
    {
        $new = clone $this;
        $new->output = $writer;

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

    public function output(): WriterInterface
    {
        return $this->output;
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
