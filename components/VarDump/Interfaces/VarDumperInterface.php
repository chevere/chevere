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

namespace Chevere\Components\VarDump\Interfaces;

use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\VarDump\Processors\ArrayProcessor;
use Chevere\Components\VarDump\Processors\BooleanProcessor;
use Chevere\Components\VarDump\Processors\FloatProcessor;
use Chevere\Components\VarDump\Processors\IntegerProcessor;
use Chevere\Components\VarDump\Processors\NullProcessor;
use Chevere\Components\VarDump\Processors\ObjectProcessor;
use Chevere\Components\VarDump\Processors\ResourceProcessor;
use Chevere\Components\VarDump\Processors\StringProcessor;
use Chevere\Components\Writers\Interfaces\WriterInterface;

interface VarDumperInterface
{
    const _FILE = '_file';
    const _CLASS = '_class';
    const _CLASS_ANON = 'class@anonymous';
    const _OPERATOR = '_operator';
    const _FUNCTION = '_function';
    const _MODIFIERS = '_modifiers';
    const _VARIABLE = '_variable';
    const _EMPHASIS = '_emphasis';

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

    public function dumpeable(): VarDumpeableInterface;

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
