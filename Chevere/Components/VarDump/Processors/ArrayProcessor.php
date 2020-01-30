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
    private array $var;

    private array $known = [];

    private $aux;

    private int $depth = 0;

    public function type(): string
    {
        return TypeInterface::ARRAY;
    }

    protected function process(): void
    {
        $this->var = $this->varFormat->dumpeable()->var();
        $this->depth = $this->varFormat->depth() + 1;
        // if ($this->isCircularRef($this->var)) {
        //     $this->val .= $this->varFormat->formatter()->highlight(
        //         VarFormatInterface::_OPERATOR,
        //         '*circular array reference*'
        //     );

        //     return;
        // }
        $count = count($this->var);
        $this->info = 'size=' . $count;
        if ($this->depth > self::MAX_DEPTH) {
            if ($count > 0) {
                $this->val .= $this->varFormat->formatter()->highlight(
                    VarFormatInterface::_OPERATOR,
                    '*max depth reached*'
                );
            }

            return;
        }
        foreach ($this->var as $k => $var) {
            $operator = $this->varFormat->formatter()->highlight(VarFormatInterface::_OPERATOR, '=>');
            $this->val .= "\n" . $this->varFormat->indentString() . ' ' . $this->varFormat->formatter()->filterEncodedChars((string) $k) . " $operator ";
            if (!$this->handleDeepth($var)) {
                break;
            }
        }
    }

    // private function isCircularRef(array $array): bool
    // {
    //     foreach ($array as $var) {
    //         if ($array === $var) {
    //             return true;
    //         }
    //         if (is_array($var)) {
    //             return $this->isCircularRef($var);
    //         }
    //     }

    //     return false;
    // }

    private function handleDeepth($var): bool
    {
        $deep = is_object($var) || is_iterable($var) ? $this->depth : $this->depth - 1;
        $newVarDump = (new VarFormat(new VarDumpeable($var), $this->varFormat->formatter()))
                    ->withIndent($this->varFormat->indent() + 1)
                    ->withDepth($deep)
                    ->withKnown($this->varFormat->known())
                    ->withProcess();
        $this->val .= $newVarDump->toString();

        return true;
    }
}
