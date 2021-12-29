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

namespace Chevere\Components\VarDump\Outputters;

use Chevere\Interfaces\VarDump\VarDumpFormatterInterface;
use Chevere\Interfaces\VarDump\VarDumpOutputterInterface;
use Chevere\Interfaces\Writer\WriterInterface;

abstract class VarDumpAbstractOutputter implements VarDumpOutputterInterface
{
    private WriterInterface $writer;

    private array $backtrace;

    private string $caller;

    public function writeCallerFile(VarDumpFormatterInterface $formatter): void
    {
        $item = $this->backtrace[0] ?? null;
        if ($item !== null && isset($item['file'])) {
            $this->writer->write(
                $formatter
                    ->highlight(
                        '_file',
                        $item['file'] . ':' . $item['line']
                    )
                . "\n"
            );
        }
    }

    final public function setUp(WriterInterface $writer, array $backtrace)
    {
        $this->writer = $writer;
        $this->backtrace = $backtrace;
        $this->caller = '';
        if ($this->backtrace[0]['class'] ?? null) {
            $this->caller .= $this->backtrace[0]['class']
                . $this->backtrace[0]['type'];
        }
        if ($this->backtrace[0]['function'] ?? null) {
            $this->caller .= $this->backtrace[0]['function'] . '()';
        }
    }

    /**
     * @codeCoverageIgnore
     */
    final public function backtrace(): array
    {
        return $this->backtrace;
    }

    final public function caller(): string
    {
        return $this->caller;
    }

    final protected function writer(): WriterInterface
    {
        return $this->writer;
    }
}
