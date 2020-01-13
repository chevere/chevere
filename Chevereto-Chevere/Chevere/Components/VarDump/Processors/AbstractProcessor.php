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

use Chevere\Components\Message\Message;
use Chevere\Components\VarDump\Interfaces\ProcessorInterface;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use InvalidArgumentException;
use TypeError;

abstract class AbstractProcessor implements ProcessorInterface
{
    protected VarDumpInterface $varDump;

    /** @var string */
    protected string $info = '';

    /** @var string */
    protected string $val = '';

    final public function __construct(VarDumpInterface $varDump)
    {
        $this->varDump = $varDump;
        $this->assertType();
        $this->process();
    }

    /**
     * @throws TypeError if the return value of VarDumpInterface::var() doesn't match the $var property type.
     */
    abstract protected function process(): void;

    final private function assertType(): void
    {
        if ($this->type() !== $this->varDump->type()) {
            throw new InvalidArgumentException(
                (new Message('Instance of %className% expects a type %expected% for the return value of %method%, type %provided% returned'))
                    ->code('%className%', static::class)
                    ->code('%expected%', $this->type())
                    ->code('%method%', get_class($this->varDump) . '::var()')
                    ->code('%provided%', $this->varDump->type())
                    ->toString()
            );
        }
    }

    final public function info(): string
    {
        return $this->info;
    }

    final public function val(): string
    {
        return $this->val;
    }
}