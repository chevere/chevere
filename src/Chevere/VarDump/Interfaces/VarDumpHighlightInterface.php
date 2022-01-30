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

namespace Chevere\VarDump\Interfaces;

use Chevere\Throwable\Exceptions\OutOfRangeException;
use Chevere\Type\Interfaces\TypeInterface;

/**
 * Describes the component in charge of highlight the var dump strings.
 */
interface VarDumpHighlightInterface
{
    public const KEYS = [
        TypeInterface::STRING,
        TypeInterface::FLOAT,
        TypeInterface::INTEGER,
        TypeInterface::BOOLEAN,
        TypeInterface::NULL,
        TypeInterface::OBJECT,
        TypeInterface::ARRAY,
        TypeInterface::RESOURCE,
        VarDumperInterface::FILE,
        VarDumperInterface::CLASS_REG,
        VarDumperInterface::OPERATOR,
        VarDumperInterface::FUNCTION,
        VarDumperInterface::MODIFIERS,
        VarDumperInterface::VARIABLE,
        VarDumperInterface::EMPHASIS,
    ];

    /**
     * Constructs a highlight instance specified by `$key`.
     *
     * @see `VarDumpHighlightInterface::KEYS`
     * @throws OutOfRangeException
     */
    public function __construct(string $key);

    /**
     * Highlights `$string`.
     */
    public function highlight(string $string): string;

    /**
     * Provide access to the color palette.
     *
     * ```php
     * return [
     *     'string' => '<color_for_string>',
     *     'float' => '<color_for_float>',
     * ];
     * ```
     */
    public static function palette(): array;
}
