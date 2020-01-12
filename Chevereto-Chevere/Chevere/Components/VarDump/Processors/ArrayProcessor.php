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

use Chevere\Components\VarDump\Interfaces\ProcessorInterface;
use Chevere\Components\VarDump\VarDump;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;

final class ArrayProcessor extends AbstractProcessor
{
    private array $var;

    public function withProcess(): ProcessorInterface
    {
        $new = clone $this;
        $new->var = $this->varDump->var();
        $new->info = 'size=' . count($new->var);
        foreach ($new->var as $k => $v) {
            $operator = $this->varDump->formatter()->applyWrap(VarDumpInterface::_OPERATOR, '=>');
            $new->val .= "\n" . $this->varDump->indentString() . ' ' . $this->varDump->formatter()->filterEncodedChars((string) $k) . " $operator ";
            $aux = $v;
            $isCircularRef = is_array($aux) && isset($aux[$k]) && $aux == $aux[$k];
            if ($isCircularRef) {
                $new->val .= $this->varDump->formatter()
                    ->applyWrap(
                        VarDumpInterface::_OPERATOR,
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
