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

    private int $depth = 0;

    public function type(): string
    {
        return TypeInterface::ARRAY;
    }

    protected function process(): void
    {
        $this->var = $this->varFormat->dumpeable()->var();
        $this->depth = $this->varFormat->depth() + 1;
        $count = count($this->var);
        $this->streamWriter->write(
            $this->varFormat->formatter()->highlight(
                $this->type(),
                $this->type()
            )
            . ' ' .
            $this->varFormat->formatter()->emphasis(
                '(size=' . $count . ')'
            )
        );
        if ($this->isCircularRef($this->var)) {
            $this->streamWriter->write(
                ' ' .
                $this->highlightOperator($this->circularReference())
            );

            return;
        }
        if ($this->depth > self::MAX_DEPTH) {
            if ($count > 0) {
                $this->streamWriter->write(
                    ' ' .
                    $this->highlightOperator($this->maxDepthReached())
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
            // Appends indent+name+operator `   key => `
            $this->streamWriter->write(
                "\n" . $this->varFormat->indentString() . ' ' . $this->varFormat->formatter()->filterEncodedChars((string) $key) . " $operator "
            );
            // Process the underlying array member value
            $this->handleDepth($var);
        }
    }

    private function handleDepth($var): void
    {
        $deep = $this->depth;
        if (is_scalar($var)) {
            $deep -= 1;
        }
        $varFormat = (new VarFormat(new VarDumpeable($var), $this->varFormat->formatter()))
            ->withDepth($deep)
            ->withIndent($this->varFormat->indent() + 1)
            ->withKnownObjects($this->varFormat->known())
            ->withProcess();
    }
}
