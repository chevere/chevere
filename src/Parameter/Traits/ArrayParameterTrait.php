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

namespace Chevere\Parameter\Traits;

use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Type\typeArray;

trait ArrayParameterTrait
{
    /**
     * @return array<mixed, mixed>
     */
    public function default(): array
    {
        return $this->default;
    }

    private function getType(): TypeInterface
    {
        return typeArray();
    }
}
