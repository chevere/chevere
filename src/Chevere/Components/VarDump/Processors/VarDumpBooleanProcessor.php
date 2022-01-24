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

final class VarDumpBooleanProcessor implements VarDumpProcessorInterface
{
    use ProcessorTrait;

    public function __construct(
        private VarDumperInterface $varDumper
    ) {
        $this->assertType();
        $this->info = $this->varDumper->dumpable()->var() ? 'true' : 'false';
    }

    public function type(): string
    {
        return TypeInterface::BOOLEAN;
    }

    public function write(): void
    {
        $this->varDumper->writer()->write(
            $this->typeHighlighted() . ' ' . $this->info
        );
    }
}
