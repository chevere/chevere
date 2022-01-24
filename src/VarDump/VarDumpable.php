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

namespace Chevere\VarDump;

use Chevere\Message\Message;
use Chevere\Throwable\Exceptions\LogicException;
use function Chevere\Type\getType;
use Chevere\VarDump\Interfaces\VarDumpableInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Interfaces\VarDumpProcessorInterface;

final class VarDumpable implements VarDumpableInterface
{
    private string $type;

    private string $processorName;

    public function __construct(
        private $var
    ) {
        $this->type = getType($this->var);
        $this->assertSetProcessorName();
    }

    public function var()
    {
        return $this->var;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function processorName(): string
    {
        return $this->processorName;
    }

    /**
     * @codeCoverageIgnore
     * @infection-ignore-all
     */
    private function assertSetProcessorName(): void
    {
        $processorName = VarDumperInterface::PROCESSORS[$this->type] ?? null;
        if (!isset($processorName)) {
            throw new LogicException(
                (new Message('No processor for variable of type %type%'))
                    ->code('%type%', $this->type)
            );
        }
        if (!is_subclass_of($processorName, VarDumpProcessorInterface::class, true)) {
            throw new LogicException(
                (new Message('Processor %processorName% must implement the %interfaceName% interface'))
                    ->code('%processorName%', $processorName)
                    ->code('%interfaceName%', VarDumpProcessorInterface::class)
            );
        }
        $this->processorName = $processorName;
    }
}
