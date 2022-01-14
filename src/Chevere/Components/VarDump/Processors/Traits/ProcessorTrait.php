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

namespace Chevere\Components\VarDump\Processors\Traits;

use Chevere\Components\Message\Message;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\VarDump\VarDumperInterface;

trait ProcessorTrait
{
    private VarDumperInterface $varDumper;

    private string $info = '';

    private int $depth = 0;

    abstract public function type(): string;

    public function depth(): int
    {
        return $this->depth;
    }

    public function info(): string
    {
        return $this->info;
    }

    public function typeHighlighted(): string
    {
        return $this->varDumper->formatter()
            ->highlight($this->type(), $this->type());
    }

    public function highlightOperator(string $string): string
    {
        return $this->varDumper->formatter()
            ->highlight(
                VarDumperInterface::OPERATOR,
                $string
            );
    }

    public function highlightParentheses(string $string): string
    {
        return $this->varDumper->formatter()->emphasis("(${string})");
    }

    public function circularReference(): string
    {
        return '*circular reference*';
    }

    public function maxDepthReached(): string
    {
        return '*max depth reached*';
    }

    private function assertType(): void
    {
        $type = new Type($this->type());
        if (!$type->validate($this->varDumper->dumpable()->var())) {
            throw new InvalidArgumentException(
                (new Message('Instance of %className% expects a type %expected% for the return value of %method%, type %provided% returned'))
                    ->code('%className%', static::class)
                    ->code('%expected%', $this->type())
                    ->code('%method%', $this->varDumper::class . '::var()')
                    ->code('%provided%', get_debug_type($this->varDumper->dumpable()->var()))
            );
        }
    }
}
