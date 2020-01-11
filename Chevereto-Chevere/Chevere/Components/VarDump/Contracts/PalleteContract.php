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

namespace Chevere\Components\VarDump\Contracts;

interface PalleteContract
{
    /**
     * Color palette used in HTML.
     */
    const HTML = [
        VarDumpContract::TYPE_STRING => '#e67e22', // orange
        VarDumpContract::TYPE_FLOAT => '#f1c40f', // yellow
        VarDumpContract::TYPE_INTEGER => '#f1c40f', // yellow
        VarDumpContract::TYPE_BOOLEAN => '#9b59b6', // purple
        VarDumpContract::TYPE_NULL => '#7f8c8d', // grey
        VarDumpContract::TYPE_OBJECT => '#e74c3c', // red
        VarDumpContract::TYPE_ARRAY => '#2ecc71', // green
        VarDumpContract::_FILE => 'inherith',
        VarDumpContract::_CLASS => '#3498db', // blue
        VarDumpContract::_OPERATOR => '#7f8c8d', // grey
        VarDumpContract::_FUNCTION => '#9b59b6', // purple
        VarDumpContract::_PRIVACY => '#9b59b6', // purple
        VarDumpContract::_VARIABLE => '#e67e22', // orange
        VarDumpContract::_EMPHASIS => '#7f8c8d',
    ];

    /**
     * Color palette used in CLI.
     */
    const CONSOLE = [
        VarDumpContract::TYPE_STRING => 'color_11',
        VarDumpContract::TYPE_FLOAT => 'color_11',
        VarDumpContract::TYPE_INTEGER => 'color_11',
        VarDumpContract::TYPE_BOOLEAN => 'color_163', // purple
        VarDumpContract::TYPE_NULL => 'color_245', // grey
        VarDumpContract::TYPE_OBJECT => 'color_39',
        VarDumpContract::TYPE_ARRAY => 'color_41', // green
        VarDumpContract::_FILE => 'default',
        VarDumpContract::_CLASS => 'color_147', // blue
        VarDumpContract::_OPERATOR => 'color_245', // grey
        VarDumpContract::_FUNCTION => 'color_39',
        VarDumpContract::_PRIVACY => 'color_133',
        VarDumpContract::_VARIABLE => 'color_208',
        VarDumpContract::_EMPHASIS => ['color_245', 'italic']
    ];
}
