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

namespace Chevere\Components\VarDump\Wrappers;

use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\VarDump\Interfaces\PalleteInterface;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;
use Chevere\Components\VarDump\Interfaces\WrapperInterface;
use Chevere\Components\VarDump\Wrappers\Traits\AssertKeyTrait;

final class HtmlWrapper implements WrapperInterface
{
    use AssertKeyTrait;

    private string $color;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->assertKey();
        $this->color = $this->pallete()[$this->key];
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
            VarDumpInterface::_FILE => 'inherith',
            VarDumpInterface::_CLASS => '#3498db', // blue
            VarDumpInterface::_OPERATOR => '#7f8c8d', // grey
            VarDumpInterface::_FUNCTION => '#9b59b6', // purple
            VarDumpInterface::_PRIVACY => '#9b59b6', // purple
            VarDumpInterface::_VARIABLE => '#e67e22', // orange
            VarDumpInterface::_EMPHASIS => '#7f8c8d',
        ];
    }

    public function wrap(string $dump): string
    {
        return '<span style="color:' . $this->color . '">' . $dump . '</span>';
    }
}
