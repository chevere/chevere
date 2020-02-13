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

final class ResourceProcessor extends AbstractProcessor
{
    public function type(): string
    {
        return TypeInterface::RESOURCE;
    }

    protected function process(): void
    {
        $this->info = 'type=' . get_resource_type($this->varDumper->dumpeable()->var());
        $this->varDumper->writer()->write(
            (string) $this->varDumper->dumpeable()->var()
            . ' '
            . $this->highlightParentheses($this->info)
        );
    }
}
