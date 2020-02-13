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
use Chevere\Components\VarDump\VarDumper;

final class ArrayProcessor extends AbstractProcessor
{
    private array $var;

    private int $depth = 0;

    public function type(): string
    {
        return TypeInterface::ARRAY;
    }

    protected function process(): void
    {
        $this->var = $this->varDumper->dumpeable()->var();
        $this->depth = $this->varDumper->depth() + 1;
        $count = count($this->var);
        $this->info = 'size=' . $count;
        $this->varDumper->writer()->write(
            $this->typeHighlighted()
            . ' '
            . $this->highlightParentheses($this->info)
        );
        if ($this->isCircularRef($this->var)) {
            $this->varDumper->writer()->write(
                ' '
                . $this->highlightOperator($this->circularReference())
            );

            return;
        }
        if ($this->depth > self::MAX_DEPTH) {
            if ($count > 0) {
                $this->varDumper->writer()->write(
                    ' '
                    . $this->highlightOperator($this->maxDepthReached())
                );
            }

            return;
        }
        $this->processMembers();
    }

    private function isCircularRef(array $array): bool
    {
        foreach ($array as $var) {
            if ($array === $var) {
                return true;
            }
            if (is_array($var)) {
                return $this->isCircularRef($var);
            }
        }

        return false;
    }

    private function processMembers(): void
    {
        $operator = $this->highlightOperator('=>');
        foreach ($this->var as $key => $var) {
            $this->varDumper->writer()->write(
                implode(' ', [
                    "\n" . $this->varDumper->indentString(),
                    $this->varDumper->formatter()->filterEncodedChars((string) $key),
                    $operator,
                    ''
                ])
            );
            $this->handleDepth($var);
        }
    }

    private function handleDepth($var): void
    {
        $deep = $this->depth;
        if (is_scalar($var)) {
            $deep -= 1;
        }
        (new VarDumper(
            $this->varDumper->writer(),
            new VarDumpeable($var),
            $this->varDumper->formatter()
        ))
            ->withDepth($deep)
            ->withIndent($this->varDumper->indent() + 1)
            ->withKnownObjects($this->varDumper->known())
            ->withProcessor();
    }
}
