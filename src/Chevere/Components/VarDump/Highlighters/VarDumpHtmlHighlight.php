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
            TypeInterface::STRING => '#ff8700',
            TypeInterface::FLOAT => '#ff8700',
            TypeInterface::INTEGER => '#ff8700',
            TypeInterface::BOOLEAN => '#ff8700',
            TypeInterface::NULL => '#ff8700',
            // yellow
            TypeInterface::OBJECT => '#fabb00',
            // green
            TypeInterface::ARRAY => '#00d700',
            // red
            TypeInterface::RESOURCE => '#ff5f5f',
            // blue
            VarDumperInterface::FILE => '#87afff',
            // light yellow
            VarDumperInterface::CLASS_REG => '#fabb00',
            // dark gray
            VarDumperInterface::OPERATOR => '#6c6c6c',
            // blue
            VarDumperInterface::FUNCTION => '#00afff',
            VarDumperInterface::VARIABLE => '#00afff',
            // pink
            VarDumperInterface::MODIFIERS => '#d75fd7',
            // dark gray italic
            VarDumperInterface::EMPHASIS => '#6c6c6c',
        ];
    }
}
