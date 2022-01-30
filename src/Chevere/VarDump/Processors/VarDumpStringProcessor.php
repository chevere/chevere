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

namespace Chevere\VarDump\Processors;

use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Interfaces\VarDumpProcessorInterface;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;

final class VarDumpStringProcessor implements VarDumpProcessorInterface
{
    use ProcessorTrait;

    public function __construct(
        private VarDumperInterface $varDumper
    ) {
        $this->assertType();
        $this->info = 'length=' . mb_strlen($this->varDumper->dumpable()->var());
    }

    public function type(): string
    {
        return TypeInterface::STRING;
    }

    public function write(): void
    {
        $this->varDumper->writer()->write(
            implode(' ', [
                $this->typeHighlighted(),
                $this->varDumper->format()->filterEncodedChars(
                    $this->varDumper->dumpable()->var()
                ),
                $this->highlightParentheses($this->info),
            ])
        );
    }
}
