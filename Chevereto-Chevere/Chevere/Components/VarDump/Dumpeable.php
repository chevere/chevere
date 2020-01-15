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

namespace Chevere\Components\VarDump;

use Chevere\Components\Message\Message;
use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\Type\Type;
use Chevere\Components\VarDump\Interfaces\DumpeableInterface;
use Chevere\Components\VarDump\Interfaces\ProcessorInterface;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use LogicException;
use function ChevereFn\varType;

/**
 * Allows to interact with dumpeable variables.
 */
final class Dumpeable implements DumpeableInterface
{
    /** @var mixed */
    private $var;

    private string $type;

    private string $processorName;

    private string $template;

    /**
     * Creates a new instance.
     *
     * @throws LogicException if it is not possible to dump the passed variable.
     */
    public function __construct($var)
    {
        $this->var = $var;
        $this->type = varType($this->var);
        $this->assertSetProcessorName();
        $this->setTemplate();
    }

    /**
     * {@inheritdoc}
     */
    public function var()
    {
        return $this->var;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): string
    {
        return $this->type;
    }

    public function template(): string
    {
        return $this->template;
    }

    /**
     * {@inheritdoc}
     */
    public function processorName(): string
    {
        return $this->processorName;
    }

    private function assertSetProcessorName(): void
    {
        $processorName = VarDumpInterface::PROCESSORS[$this->type] ?? null;
        if (!isset($processorName)) {
            throw new LogicException(
                (new Message('No processor for variable of type %type%'))
                    ->code('%type%', $this->type)
                    ->toString()
            );
        }
        if (!is_subclass_of($processorName, ProcessorInterface::class, true)) {
            throw new LogicException(
                (new Message('Processor %processorName% must implement the %interfaceName% interface'))
                    ->code('%processorName%', $processorName)
                    ->code('%interfaceName%', ProcessorInterface::class)
                    ->toString()
            );
        }
        $this->processorName = $processorName;
    }

    private function setTemplate(): void
    {
        switch ($this->type) {
            case TypeInterface::ARRAY:
            case TypeInterface::OBJECT:
                $this->template = '%type% %info% %val%';
                break;
            default:
                $this->template = '%type% %val% %info%';
                break;
        }
    }
}
