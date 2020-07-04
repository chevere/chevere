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

    private string $key;

    private string $color;

    public function __construct(string $key)
    {
        $this->assertKey($key);
        $this->key = $key;
        $this->color = $this->pallet()[$this->key] ?? 'inherit';
    }

    public function wrap(string $dump): string
    {
        return '<span style="color:' . $this->color . '">' . $dump . '</span>';
    }

    public function pallet(): array
    {
        return [
            TypeInterface::STRING => '#e67e22', // orange
            TypeInterface::FLOAT => '#f1c40f', // yellow
            TypeInterface::INTEGER => '#f1c40f', // yellow
            TypeInterface::BOOLEAN => '#9b59b6', // purple
            TypeInterface::NULL => '#7f8c8d', // grey
            TypeInterface::OBJECT => '#e74c3c', // red
            TypeInterface::ARRAY => '#2ecc71', // green
            TypeInterface::RESOURCE => '#3498db', // blue
            VarDumperInterface::FILE => 'inherit',
            VarDumperInterface::CLASS_REG => '#3498db', // blue
            VarDumperInterface::OPERATOR => '#7f8c8d', // grey
            VarDumperInterface::FUNCTION => '#9b59b6', // purple
            VarDumperInterface::MODIFIERS => '#9b59b6', // purple
            VarDumperInterface::VARIABLE => '#e67e22', // orange
            VarDumperInterface::EMPHASIS => '#7f8c8d',
        ];
    }
}
