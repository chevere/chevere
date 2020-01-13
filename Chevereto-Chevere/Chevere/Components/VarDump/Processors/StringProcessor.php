<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump\Processors;

use Chevere\Components\VarDump\Interfaces\VarDumpInterface;

final class StringProcessor extends AbstractProcessor
{
    private string $var;

    public function type(): string
    {
        return VarDumpInterface::TYPE_STRING;
    }

    protected function process(): void
    {
        $this->var = $this->varDump->var();
        $this->info = 'length=' . strlen($this->var);
        $this->val = $this->varDump->formatter()->filterEncodedChars($this->var);
    }
}