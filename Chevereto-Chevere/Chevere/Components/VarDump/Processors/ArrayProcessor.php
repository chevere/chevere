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

use Chevere\Components\VarDump\Contracts\ProcessorContract;
use Chevere\Components\VarDump\VarDump;
use Chevere\Components\VarDump\Contracts\VarDumpContract;

final class ArrayProcessor extends AbstractProcessor
{
    private array $var;

    public function withProcess(): ProcessorContract
    {
        $new = clone $this;
        $new->var = $this->varDump->var();
        $new->info = 'size=' . count($new->var);
        foreach ($new->var as $k => $v) {
            $operator = $this->varDump->formatter()->applyWrap(VarDumpContract::_OPERATOR, '=>');
            $new->val .= "\n" . $this->varDump->indentString() . ' ' . $this->varDump->formatter()->filterEncodedChars((string) $k) . " $operator ";
            $aux = $v;
            $isCircularRef = is_array($aux) && isset($aux[$k]) && $aux == $aux[$k];
            if ($isCircularRef) {
                $new->val .= $this->varDump->formatter()
                    ->applyWrap(
                        VarDumpContract::_OPERATOR,
                        '(' . $this->varDump->formatter()->applyEmphasis('circular array reference') . ')'
                    );
            } else {
                $newVarDump = (new VarDump($this->varDump->formatter()))
                    ->withDontDump(...$this->varDump->dontDump())
                    ->withVar($aux)
                    ->withIndent($this->varDump->indent() + 1)
                    ->process();
                $new->val .= $newVarDump->toString();
            }
        }

        return $new;
    }
}
