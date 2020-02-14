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

use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\VarDump\Interfaces\ProcessorInterface;
use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\Processors\Traits\ProcessorTrait;

final class BooleanProcessor implements ProcessorInterface
{
    use ProcessorTrait;

    public function __construct(VarDumperInterface $varDumper)
    {
        $this->varDumper = $varDumper;
        $this->assertType();
        $this->info = $this->varDumper->dumpeable()->var() ? 'true' : 'false';
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
