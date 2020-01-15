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
use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\Type\Type;
use Chevere\Components\VarDump\Interfaces\ProcessorInterface;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use InvalidArgumentException;
use TypeError;
use function ChevereFn\varType;

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

    abstract public function type(): string;

    final private function assertType(): void
    {
        $type = new Type($this->type());
        if (!$type->validate($this->varDump->dumpeable()->var())) {
            throw new InvalidArgumentException(
                (new Message('Instance of %className% expects a type %expected% for the return value of %method%, type %provided% returned'))
                    ->code('%className%', static::class)
                    ->code('%expected%', $this->type())
                    ->code('%method%', get_class($this->varDump) . '::var()')
                    ->code('%provided%', varType($this->varDump->dumpeable()->var()))
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
