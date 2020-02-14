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
use Chevere\Components\VarDump\Interfaces\ProcessorInterface;
use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\Processors\Traits\ProcessorTrait;
use Chevere\Components\VarDump\VarDumpeable;
use Chevere\Components\VarDump\VarDumper;

final class ArrayProcessor implements ProcessorInterface
{
    use ProcessorTrait;

    private array $var;

    private int $depth = 0;

    private int $count = 0;

    public function __construct(VarDumperInterface $varDumper)
    {
        $this->varDumper = $varDumper;
        $this->assertType();
        $this->var = $this->varDumper->dumpeable()->var();
        $this->depth = $this->varDumper->depth() + 1;
        $this->count = count($this->var);
        $this->info = 'size=' . $this->count;
    }

    public function type(): string
    {
        return TypeInterface::ARRAY;
    }

    public function write(): void
    {
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
            if ($this->count > 0) {
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
            $this->varDumper->formatter(),
            new VarDumpeable($var),
        ))
            ->withDepth($deep)
            ->withIndent($this->varDumper->indent() + 1)
            ->withKnownObjects($this->varDumper->known())
            ->withProcessor();
    }
}
