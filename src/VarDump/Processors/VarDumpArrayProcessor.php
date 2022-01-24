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

namespace Chevere\VarDump\Processors;

use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Interfaces\VarDumpProcessorInterface;
use Chevere\VarDump\Processors\Traits\ProcessorTrait;
use Chevere\VarDump\VarDumpable;
use Chevere\VarDump\VarDumper;

final class VarDumpArrayProcessor implements VarDumpProcessorInterface
{
    use ProcessorTrait;

    private array $var;

    private int $count = 0;

    public function __construct(
        private VarDumperInterface $varDumper
    ) {
        $this->assertType();
        $this->var = $this->varDumper->dumpable()->var();
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
                    "\n" . $this->varDumper->indentString() .
                    $this->varDumper->format()->filterEncodedChars((string) $key),
                    $operator,
                    '',
                ])
            );
            $this->handleDepth($var);
        }
    }

    private function handleDepth($var): void
    {
        $deep = $this->depth;
        if (is_scalar($var)) {
            --$deep;
        }
        (new VarDumper(
            $this->varDumper->writer(),
            $this->varDumper->format(),
            new VarDumpable($var),
        ))
            ->withDepth($deep)
            ->withIndent($this->varDumper->indent())
            ->withKnownObjects($this->varDumper->known())
            ->withProcess();
    }
}
