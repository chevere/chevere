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
use Chevere\Components\VarDump\Interfaces\VarInfoInterface;
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
            VarInfoInterface::_FILE => 'inherith',
            VarInfoInterface::_CLASS => '#3498db', // blue
            VarInfoInterface::_OPERATOR => '#7f8c8d', // grey
            VarInfoInterface::_FUNCTION => '#9b59b6', // purple
            VarInfoInterface::_PRIVACY => '#9b59b6', // purple
            VarInfoInterface::_VARIABLE => '#e67e22', // orange
            VarInfoInterface::_EMPHASIS => '#7f8c8d',
        ];
    }

    public function wrap(string $dump): string
    {
        return '<span style="color:' . $this->color . '">' . $dump . '</span>';
    }
}
