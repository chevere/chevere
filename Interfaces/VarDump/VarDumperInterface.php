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

use Chevere\Components\VarDump\Processors\ArrayProcessor;
use Chevere\Components\VarDump\Processors\BooleanProcessor;
use Chevere\Components\VarDump\Processors\FloatProcessor;
use Chevere\Components\VarDump\Processors\IntegerProcessor;
use Chevere\Components\VarDump\Processors\NullProcessor;
use Chevere\Components\VarDump\Processors\ObjectProcessor;
use Chevere\Components\VarDump\Processors\ResourceProcessor;
use Chevere\Components\VarDump\Processors\StringProcessor;
use Chevere\Interfaces\Type\TypeInterface;
use Chevere\Interfaces\Writers\WriterInterface;

interface VarDumperInterface
{
    const FILE = '_file';
    const CLASS_REG = '_class';
    const CLASS_ANON = 'class@anonymous';
    const OPERATOR = '_operator';
    const FUNCTION = '_function';
    const MODIFIERS = '_modifiers';
    const VARIABLE = '_variable';
    const EMPHASIS = '_emphasis';

    /** @var array [ProcessorInterface $processor,] */
    const PROCESSORS = [
        TypeInterface::BOOLEAN => BooleanProcessor::class,
        TypeInterface::ARRAY => ArrayProcessor::class,
        TypeInterface::OBJECT => ObjectProcessor::class,
        TypeInterface::INTEGER => IntegerProcessor::class,
        TypeInterface::STRING => StringProcessor::class,
        TypeInterface::FLOAT => FloatProcessor::class,
        TypeInterface::NULL => NullProcessor::class,
        TypeInterface::RESOURCE => ResourceProcessor::class,
    ];

    public function writer(): WriterInterface;

    public function dumpable(): VarDumpableInterface;

    /**
     * Provides access to the FormatterInterface instance.
     */
    public function formatter(): FormatterInterface;

    /**
     * Return an instance with the specified $indent.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified $indent.
     */
    public function withIndent(int $indent): VarDumperInterface;

    /**
     * Provides access to the instance $indent.
     */
    public function indent(): int;

    /**
     * Provides access to the instance $indentString.
     */
    public function indentString(): string;

    /**
     * Return an instance with the specified $depth.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified $depth.
     */
    public function withDepth(int $depth): VarDumperInterface;

    /**
     * Provides access to the instance $depth.
     */
    public function depth(): int;

    /**
     * Return an instance with the specified known object IDs.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified known object IDs.
     */
    public function withKnownObjects(array $known): VarDumperInterface;

    public function known(): array;

    /**
     * Process the var dump operation.
     */
    public function withProcessor(): VarDumperInterface;
}
