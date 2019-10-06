<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\VarDump\Processors;

use Chevere\Contracts\VarDump\ProcessorContract;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;
use Chevere\VarDump\VarDump;

final class ArrayProcessor implements ProcessorContract
{
    use ProcessorTrait;

    public function __construct(array $expression, VarDump $varDump)
    {
        $this->val = '';
        $this->parentheses = '';
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
                $new = $varDump
                    ->respawn()
                    ->withDump($aux, $varDump->indent());
                $this->val .= $new->toString();
            }
        }
        $this->parentheses = 'size=' . count($expression);
    }
}
