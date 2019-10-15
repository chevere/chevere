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

namespace Chevere\Components\VarDump\src;

use Chevere\Components\VarDump\VarDump;

abstract class Pallete
{
    /**
     * Color palette used in HTML.
     */
    const PALETTE = [
        VarDump::TYPE_STRING => '#e67e22', // orange
        VarDump::TYPE_FLOAT => '#f1c40f', // yellow
        VarDump::TYPE_INTEGER => '#f1c40f', // yellow
        VarDump::TYPE_BOOLEAN => '#9b59b6', // purple
        VarDump::TYPE_NULL => '#7f8c8d', // grey
        VarDump::TYPE_OBJECT => '#e74c3c', // red
        VarDump::TYPE_ARRAY => '#2ecc71', // green
        VarDump::_FILE => null,
        VarDump::_CLASS => '#3498db', // blue
        VarDump::_OPERATOR => '#7f8c8d', // grey
        VarDump::_FUNCTION => '#9b59b6', // purple
    ];

    /**
     * Color palette used in CLI.
     */
    const CONSOLE = [
        VarDump::TYPE_STRING => 'color_136', // yellow
        VarDump::TYPE_FLOAT => 'color_136', // yellow
        VarDump::TYPE_INTEGER => 'color_136', // yellow
        VarDump::TYPE_BOOLEAN => 'color_127', // purple
        VarDump::TYPE_NULL => 'color_245', // grey
        VarDump::TYPE_OBJECT => 'color_167', // red
        VarDump::TYPE_ARRAY => 'color_41', // green
        VarDump::_FILE => null,
        VarDump::_CLASS => 'color_147', // blue
        VarDump::_OPERATOR => 'color_245', // grey
        VarDump::_FUNCTION => 'color_127', // purple
    ];
}
