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

use Chevere\Components\VarDump\VarDump;
use Chevere\Components\VarDump\Contracts\VarDumpContract;

final class ArrayProcessor extends AbstractProcessor
{
    private array $var;

    public function __construct(VarDumpContract $varDump)
    {
        $this->var = $varDump->var();
        $this->val = '';
        $this->info = 'size=' . count($this->var);
        foreach ($this->var as $k => $v) {
            $operator = $varDump->formatter()->applyWrap($varDump::_OPERATOR, '=>');
            $this->val .= "\n" . $varDump->indentString() . ' ' . $varDump->formatter()->filterEncodedChars((string) $k) . " $operator ";
            $aux = $v;
            $isCircularRef = is_array($aux) && isset($aux[$k]) && $aux == $aux[$k];
            if ($isCircularRef) {
                $this->val .= $varDump->formatter()
                    ->applyWrap(
                        VarDump::_OPERATOR,
                        '(' . $varDump->formatter()->applyEmphasis('circular array reference') . ')'
                    );
            } else {
                $new = (new VarDump($varDump->formatter()))
                    ->withDontDump(...$varDump->dontDump())
                    ->withVar($aux)
                    ->withIndent($varDump->indent() + 1)
                    ->process();
                $this->val .= $new->toString();
            }
        }
    }
}
