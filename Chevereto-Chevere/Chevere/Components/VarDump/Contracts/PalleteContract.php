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

use Chevere\Components\VarDump\VarDump;

interface PalleteContract
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
        VarDump::_FILE => 'inherith',
        VarDump::_CLASS => '#3498db', // blue
        VarDump::_OPERATOR => '#7f8c8d', // grey
        VarDump::_FUNCTION => '#9b59b6', // purple
        VarDump::_PRIVACY => '#9b59b6', // purple
        VarDump::_VARIABLE => '#e67e22', // orange
        'emphasis' => '#7f8c8d',
    ];

    /**
     * Color palette used in CLI.
     */
    const CONSOLE = [
        VarDump::TYPE_STRING => 'color_11',
        VarDump::TYPE_FLOAT => 'color_11',
        VarDump::TYPE_INTEGER => 'color_11',
        VarDump::TYPE_BOOLEAN => 'color_163', // purple
        VarDump::TYPE_NULL => 'color_245', // grey
        VarDump::TYPE_OBJECT => 'color_39',
        VarDump::TYPE_ARRAY => 'color_41', // green
        VarDump::_FILE => 'default',
        VarDump::_CLASS => 'color_147', // blue
        VarDump::_OPERATOR => 'color_245', // grey
        VarDump::_FUNCTION => 'color_39',
        VarDump::_PRIVACY => 'color_133',
        VarDump::_VARIABLE => 'color_208',
        'emphasis' => ['color_245', 'italic']
    ];
}
