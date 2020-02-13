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

final class IntegerProcessor extends AbstractProcessor
{
    public function type(): string
    {
        return TypeInterface::INTEGER;
    }

    protected function process(): void
    {
        $stringVar = (string) $this->varProcess->dumpeable()->var();
        $this->info = 'length=' . strlen($stringVar);
        $this->varProcess->writer()->write(
            $this->typeHighlighted()
            . ' '
            . $this->varProcess->formatter()->filterEncodedChars($stringVar)
            . ' '
            . $this->highlightParentheses($this->info)
        );
    }
}
