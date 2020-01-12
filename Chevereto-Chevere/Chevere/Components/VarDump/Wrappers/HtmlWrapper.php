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

namespace Chevere\Components\VarDump\Wrappers;

use Chevere\Components\VarDump\Interfaces\PalleteInterface;

final class HtmlWrapper extends AbstractWrapper
{
    private string $color;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->pallete = PalleteInterface::HTML;
        $this->assertKey();
        $this->color = $this->pallete[$this->key];
    }

    public function wrap(string $dump): string
    {
        return '<span style="color:' . $this->color . '">' . $dump . '</span>';
    }
}
