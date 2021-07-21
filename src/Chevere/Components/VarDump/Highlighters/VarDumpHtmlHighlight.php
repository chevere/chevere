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

namespace Chevere\Components\VarDump\Highlighters;

use Chevere\Components\VarDump\Highlighters\Traits\AssertKeyTrait;
use Chevere\Interfaces\Type\TypeInterface;
use Chevere\Interfaces\VarDump\VarDumperInterface;
use Chevere\Interfaces\VarDump\VarDumpHighlightInterface;

final class VarDumpHtmlHighlight implements VarDumpHighlightInterface
{
    use AssertKeyTrait;

    private string $color;

    public function __construct(
        private string $key
    ) {
        $this->assertKey($key);
        $this->color = $this->palette()[$this->key] ?? 'inherit';
    }

    public function highlight(string $dump): string
    {
        return '<span style="color:' . $this->color . '">' . $dump . '</span>';
    }

    public function palette(): array
    {
        return [
            // orange
            TypeInterface::STRING => '#e67e22',
            // yellow
            TypeInterface::FLOAT => '#f1c40f',
            // yellow
            TypeInterface::INTEGER => '#f1c40f',
            // purple
            TypeInterface::BOOLEAN => '#9b59b6',
            // grey
            TypeInterface::NULL => '#7f8c8d',
            // red
            TypeInterface::OBJECT => '#e74c3c',
            // green
            TypeInterface::ARRAY => '#2ecc71',
            // blue
            TypeInterface::RESOURCE => '#3498db',
            VarDumperInterface::FILE => 'inherit',
            // blue
            VarDumperInterface::CLASS_REG => '#3498db',
            // grey
            VarDumperInterface::OPERATOR => '#7f8c8d',
            // purple
            VarDumperInterface::FUNCTION => '#9b59b6',
            // purple
            VarDumperInterface::MODIFIERS => '#9b59b6',
            // orange
            VarDumperInterface::VARIABLE => '#e67e22',
            VarDumperInterface::EMPHASIS => '#7f8c8d',
        ];
    }
}
