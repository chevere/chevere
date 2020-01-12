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

use Chevere\Components\VarDump\Interfaces\ProcessorInterface;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use LogicException;

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
        $this->assertVarDump();
    }

    final public function info(): string
    {
        return $this->info;
    }

    final public function val(): string
    {
        return $this->val;
    }

    /**
     * @throws TypeError If the return of varDump->var() doesn't match the concrete property type.
     */
    abstract public function withProcess(): ProcessorInterface;

    final private function assertVarDump(): void
    {
        if (!$this->varDump->hasVar()) {
            throw new LogicException('VarDumpContract must contain a var.');
        }
    }
}
