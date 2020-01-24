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

interface PalleteInterface
{
    /**
     * Color palette used in HTML.
     */
    const HTML = [
        TypeInterface::STRING => '#e67e22', // orange
        TypeInterface::FLOAT => '#f1c40f', // yellow
        TypeInterface::INTEGER => '#f1c40f', // yellow
        TypeInterface::BOOLEAN => '#9b59b6', // purple
        TypeInterface::NULL => '#7f8c8d', // grey
        TypeInterface::OBJECT => '#e74c3c', // red
        TypeInterface::ARRAY => '#2ecc71', // green
        VarDumpInterface::_FILE => 'inherith',
        VarDumpInterface::_CLASS => '#3498db', // blue
        VarDumpInterface::_OPERATOR => '#7f8c8d', // grey
        VarDumpInterface::_FUNCTION => '#9b59b6', // purple
        VarDumpInterface::_PRIVACY => '#9b59b6', // purple
        VarDumpInterface::_VARIABLE => '#e67e22', // orange
        VarDumpInterface::_EMPHASIS => '#7f8c8d',
    ];

    /**
     * Color palette used in CLI.
     */
    const CONSOLE = [
        TypeInterface::STRING => 'color_11',
        TypeInterface::FLOAT => 'color_11',
        TypeInterface::INTEGER => 'color_11',
        TypeInterface::BOOLEAN => 'color_163', // purple
        TypeInterface::NULL => 'color_245', // grey
        TypeInterface::OBJECT => 'color_39',
        TypeInterface::ARRAY => 'color_41', // green
        VarDumpInterface::_FILE => 'default',
        VarDumpInterface::_CLASS => 'color_147', // blue
        VarDumpInterface::_OPERATOR => 'color_245', // grey
        VarDumpInterface::_FUNCTION => 'color_39',
        VarDumpInterface::_PRIVACY => 'color_133',
        VarDumpInterface::_VARIABLE => 'color_208',
        VarDumpInterface::_EMPHASIS => ['color_245', 'italic']
    ];
}
