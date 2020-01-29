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
use Chevere\Components\VarDump\Interfaces\VarFormatInterface;

final class ArrayProcessor extends AbstractProcessor
{
    public function type(): string
    {
        return TypeInterface::ARRAY;
    }

    protected function process(): void
    {
        $this->info = 'size=' . count($this->varInfo->dumpeable()->var());
        foreach ($this->varInfo->dumpeable()->var() as $k => $v) {
            $operator = $this->varInfo->formatter()->highlight(VarFormatInterface::_OPERATOR, '=>');
            $this->val .= "\n" . $this->varInfo->indentString() . ' ' . $this->varInfo->formatter()->filterEncodedChars((string) $k) . " $operator ";
            $aux = $v;
            $isCircularRef = is_array($aux) && isset($aux[$k]) && $aux == $aux[$k];
            if ($isCircularRef) {
                $this->val .= $this->varInfo->formatter()
                    ->highlight(
                        VarFormatInterface::_OPERATOR,
                        '(' . $this->varInfo->formatter()->emphasis('circular array reference') . ')'
                    );
            } else {
                $newVarDump = (new VarFormat(new VarDumpeable($aux), $this->varInfo->formatter()))
                    // ->withDontDump(...$this->varDump->dontDump())
                    ->withIndent($this->varInfo->indent() + 1)
                    ->withProcess();
                $this->val .= $newVarDump->toString();
            }
        }
    }
}
