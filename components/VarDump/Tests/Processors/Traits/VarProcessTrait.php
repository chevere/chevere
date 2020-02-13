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

namespace Chevere\Components\VarDump\Tests\Processors\Traits;

use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\Interfaces\VarProcessInterface;
use Chevere\Components\VarDump\VarDumpeable;
use Chevere\Components\VarDump\VarProcess;
use Chevere\Components\Writers\Interfaces\WriterInterface;
use Chevere\Components\Writers\StreamWriter;
use function GuzzleHttp\Psr7\stream_for;

trait VarProcessTrait
{
    private WriterInterface $writer;

    private FormatterInterface $formater;

    public function setUp(): void
    {
        $this->writer = new StreamWriter(stream_for(''));
        $this->formater = new PlainFormatter;
    }

    private function getWriter(): WriterInterface
    {
        return $this->writer;
    }

    private function getVarProcess($var): VarProcessInterface
    {
        return new VarProcess(
            $this->writer,
            new VarDumpeable($var),
            $this->formater
        );
    }
}
