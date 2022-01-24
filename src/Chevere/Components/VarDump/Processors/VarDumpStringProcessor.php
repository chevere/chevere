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

namespace Chevere\Components\VarDump\Processors;

use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\Interfaces\VarDumpProcessorInterface;
use Chevere\Components\VarDump\Processors\Traits\ProcessorTrait;
use Chevere\Interfaces\Type\TypeInterface;

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
