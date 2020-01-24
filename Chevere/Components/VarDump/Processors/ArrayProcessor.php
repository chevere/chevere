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
use Chevere\Components\VarDump\VarDumpeable;
use Chevere\Components\VarDump\VarFormat;
use Chevere\Components\VarDump\Interfaces\VarInfoInterface;

final class ArrayProcessor extends AbstractProcessor
{
    public function type(): string
    {
        return TypeInterface::ARRAY;
    }

    protected function process(): void
    {
        $this->info = 'size=' . count($this->varDump->dumpeable()->var());
        foreach ($this->varDump->dumpeable()->var() as $k => $v) {
            $operator = $this->varDump->formatter()->applyWrap(VarInfoInterface::_OPERATOR, '=>');
            $this->val .= "\n" . $this->varDump->indentString() . ' ' . $this->varDump->formatter()->filterEncodedChars((string) $k) . " $operator ";
            $aux = $v;
            $isCircularRef = is_array($aux) && isset($aux[$k]) && $aux == $aux[$k];
            if ($isCircularRef) {
                $this->val .= $this->varDump->formatter()
                    ->applyWrap(
                        VarInfoInterface::_OPERATOR,
                        '(' . $this->varDump->formatter()->applyEmphasis('circular array reference') . ')'
                    );
            } else {
                $newVarDump = (new VarFormat(new VarDumpeable($aux), $this->varDump->formatter()))
                    ->withDontDump(...$this->varDump->dontDump())
                    ->withIndent($this->varDump->indent() + 1)
                    ->withProcess();
                $this->val .= $newVarDump->toString();
            }
        }
    }
}
