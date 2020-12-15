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

namespace Chevere\Components\Parameter;

use Chevere\Components\Parameter\Traits\ParameterTrait;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Type\TypeInterface;
use Ds\Set;

final class Parameter implements ParameterInterface
{
    use ParameterTrait;

    public function __construct(TypeInterface $type)
    {
        $this->type = $type;
        $this->attributes = new Set();
    }
}
