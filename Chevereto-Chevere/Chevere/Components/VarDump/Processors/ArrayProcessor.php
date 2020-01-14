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

use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\VarDump\VarDump;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;

final class ArrayProcessor extends AbstractProcessor
{
    public function type(): string
    {
        return TypeInterface::ARRAY;
    }

    protected function process(): void
    {
        $this->info = 'size=' . count($this->varDump->var());
        foreach ($this->varDump->var() as $k => $v) {
            $operator = $this->varDump->formatter()->applyWrap(VarDumpInterface::_OPERATOR, '=>');
            $this->val .= "\n" . $this->varDump->indentString() . ' ' . $this->varDump->formatter()->filterEncodedChars((string) $k) . " $operator ";
            $aux = $v;
            $isCircularRef = is_array($aux) && isset($aux[$k]) && $aux == $aux[$k];
            if ($isCircularRef) {
                $this->val .= $this->varDump->formatter()
                    ->applyWrap(
                        VarDumpInterface::_OPERATOR,
                        '(' . $this->varDump->formatter()->applyEmphasis('circular array reference') . ')'
                    );
            } else {
                $newVarDump = (new VarDump($aux, $this->varDump->formatter()))
                    ->withDontDump(...$this->varDump->dontDump())
                    ->withIndent($this->varDump->indent() + 1)
                    ->process();
                $this->val .= $newVarDump->toString();
            }
        }
    }
}
