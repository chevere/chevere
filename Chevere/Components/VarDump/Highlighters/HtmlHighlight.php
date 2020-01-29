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

use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\VarDump\Interfaces\VarFormatInterface;
use Chevere\Components\VarDump\Interfaces\HighlightInterface;
use Chevere\Components\VarDump\Highlighters\Traits\AssertKeyTrait;

final class HtmlHighlight implements HighlightInterface
{
    use AssertKeyTrait;

    private string $color;

    public function __construct(string $key)
    {
        $this->assertKey($key);
        $this->key = $key;
        $this->color = $this->pallete[$this->key] ?? 'inherith';
    }

    public function wrap(string $dump): string
    {
        return '<span style="color:' . $this->color . '">' . $dump . '</span>';
    }

    public function pallete(): array
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
            VarFormatInterface::_FILE => 'inherith',
            VarFormatInterface::_CLASS => '#3498db', // blue
            VarFormatInterface::_OPERATOR => '#7f8c8d', // grey
            VarFormatInterface::_FUNCTION => '#9b59b6', // purple
            VarFormatInterface::_PRIVACY => '#9b59b6', // purple
            VarFormatInterface::_VARIABLE => '#e67e22', // orange
            VarFormatInterface::_EMPHASIS => '#7f8c8d',
        ];
    }
}
