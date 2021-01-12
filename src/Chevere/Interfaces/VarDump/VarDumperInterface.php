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

namespace Chevere\Interfaces\VarDump;

use Chevere\Components\VarDump\Processors\VarDumpArrayProcessor;
use Chevere\Components\VarDump\Processors\VarDumpBooleanProcessor;
use Chevere\Components\VarDump\Processors\VarDumpFloatProcessor;
use Chevere\Components\VarDump\Processors\VarDumpIntegerProcessor;
use Chevere\Components\VarDump\Processors\VarDumpNullProcessor;
use Chevere\Components\VarDump\Processors\VarDumpObjectProcessor;
use Chevere\Components\VarDump\Processors\VarDumpResourceProcessor;
use Chevere\Components\VarDump\Processors\VarDumpStringProcessor;
use Chevere\Interfaces\Type\TypeInterface;
use Chevere\Interfaces\Writer\WriterInterface;
use Ds\Set;

/**
 * Describes the component in charge of handling variable dumping process.
 */
interface VarDumperInterface
{
    public const FILE = '_file';

    public const CLASS_REG = '_class';

    public const CLASS_ANON = 'class@anonymous';

    public const OPERATOR = '_operator';

    public const FUNCTION = '_function';

    public const MODIFIERS = '_modifiers';

    public const VARIABLE = '_variable';

    public const EMPHASIS = '_emphasis';

    /**
     * @var array [ProcessorInterface,]
     */
    public const PROCESSORS = [
        TypeInterface::BOOL => VarDumpBooleanProcessor::class,
        TypeInterface::ARRAY => VarDumpArrayProcessor::class,
        TypeInterface::OBJECT => VarDumpObjectProcessor::class,
        TypeInterface::INT => VarDumpIntegerProcessor::class,
        TypeInterface::STRING => VarDumpStringProcessor::class,
        TypeInterface::FLOAT => VarDumpFloatProcessor::class,
        TypeInterface::NULL => VarDumpNullProcessor::class,
        TypeInterface::RESOURCE => VarDumpResourceProcessor::class,
    ];

    public function __construct(
        WriterInterface $writer,
        VarDumpFormatterInterface $formatter,
        VarDumpableInterface $dumpable
    );

    /**
     * Provides access to the `$writer` instance.
     */
    public function writer(): WriterInterface;

    /**
     * Provides access to the `$formatter` instance.
     */
    public function formatter(): VarDumpFormatterInterface;

    /**
     * Provides access to the `$dumpable` instance.
     */
    public function dumpable(): VarDumpableInterface;

    /**
     * Return an instance with the specified `$indent`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$indent`.
     */
    public function withIndent(int $indent): self;

    /**
     * Provides access to the instance indent value.
     */
    public function indent(): int;

    /**
     * Provides access to the instance indent string.
     */
    public function indentString(): string;

    /**
     * Return an instance with the specified `$depth`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$depth`.
     */
    public function withDepth(int $depth): self;

    /**
     * Provides access to the instance `$depth`.
     */
    public function depth(): int;

    /**
     * Return an instance with the specified `$known` object IDs.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$known` object IDs.
     */
    public function withKnownObjects(Set $known): self;

    /**
     * Provides access to the known object ids.
     */
    public function known(): Set;

    /**
     * Process the dump.
     */
    public function withProcess(): self;
}
