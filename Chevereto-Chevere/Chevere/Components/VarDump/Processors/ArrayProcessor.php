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

use Chevere\Components\VarDump\Processors\Traits\ProcessorTrait;
use Chevere\Components\VarDump\VarDump;
use Chevere\Components\VarDump\Contracts\ProcessorContract;

final class ArrayProcessor implements ProcessorContract
{
    use ProcessorTrait;

    public function __construct(array $expression, VarDump $varDump)
    {
        $this->val = '';
        $this->info = '';
        foreach ($expression as $k => $v) {
            $operator = $varDump->formatter()->wrap(VarDump::_OPERATOR, '=>');
            $this->val .= "\n" . $varDump->indentString() . ' ' . $varDump->formatter()->getEncodedChars((string) $k) . " $operator ";
            $aux = $v;
            $isCircularRef = is_array($aux) && isset($aux[$k]) && $aux == $aux[$k];
            if ($isCircularRef) {
                $this->val .= $varDump->formatter()->wrap(
                    VarDump::_OPERATOR,
                    '(' . $varDump->formatter()->getEmphasis('circular array reference') . ')'
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
        $this->info = 'size=' . count($expression);
    }
}
