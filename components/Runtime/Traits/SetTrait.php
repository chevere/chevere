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

namespace Chevere\Components\Runtime\Traits;

use Chevere\Components\Str\Str;

trait SetTrait
{
    protected string $value;

    public function value(): string
    {
        return $this->value;
    }

    public function name(): string
    {
        $explode = explode('\\', static::class);
        $name = (string) (new Str(end($explode)))->replaceFirst('Set', '');

        return lcfirst($name);
    }
}
