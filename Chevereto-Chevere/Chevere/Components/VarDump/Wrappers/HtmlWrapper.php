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

use Chevere\Components\VarDump\Contracts\PalleteContract;
use Chevere\Components\VarDump\Contracts\WrapperContract;
use Chevere\Components\VarDump\Wrappers\Traits\AssertKeyTrait;

final class HtmlWrapper implements WrapperContract
{
    use AssertKeyTrait;

    private string $color;

    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->assertKey();
        $this->color = $this->pallete()[$this->key];
    }

    public function wrap(string $dump): string
    {
        return '<span style="color:' . $this->color . '">' . $dump . '</span>';
    }

    public function pallete(): array
    {
        return PalleteContract::PALETTE;
    }
}
