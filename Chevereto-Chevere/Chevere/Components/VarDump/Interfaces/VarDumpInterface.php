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
use ReflectionProperty;

interface VarDumpInterface
{
    const _FILE = '_file';
    const _CLASS = '_class';
    const _CLASS_ANON = 'class@anonymous';
    const _OPERATOR = '_operator';
    const _FUNCTION = '_function';
    const _PRIVACY = '_privacy';
    const _VARIABLE = '_variable';
    const _EMPHASIS = '_emphasis';

    const PROPERTIES_REFLECTION_MAP = [
        'public' => ReflectionProperty::IS_PUBLIC,
        'protected' => ReflectionProperty::IS_PROTECTED,
        'private' => ReflectionProperty::IS_PRIVATE,
        'static' => ReflectionProperty::IS_STATIC,
    ];

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

    public function __construct($var, FormatterInterface $formatter);

    /**
     * Provides access to the FormatterInterface instance.
     */
    public function formatter(): FormatterInterface;

    /**
     * Return an instance with the specified don't dump class names.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified don't dump class names.
     */
    public function withDontDump(string ...$dontDump): VarDumpInterface;

    /**
     * Provides access to the dont't dump array.
     *
     * @return array [className,]
     */
    public function dontDump(): array;

    /**
     * Provides access to the instance var.
     */
    public function var();

    /**
     * Return an instance with the specified $indent.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified $indent.
     */
    public function withIndent(int $indent): VarDumpInterface;

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
    public function withDepth(int $depth): VarDumpInterface;

    /**
     * Provides access to the instance $depth.
     */
    public function depth(): int;

    /**
     * Process the var dump operation.
     */
    public function process(): VarDumpInterface;

    /**
     * Provides access to the instance $output.
     */
    public function toString(): string;
}
