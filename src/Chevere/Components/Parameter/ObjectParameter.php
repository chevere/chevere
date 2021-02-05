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
use function Chevere\Components\Type\typeObject;
use Chevere\Interfaces\Parameter\ObjectParameterInterface;
use Ds\Set;

final class ObjectParameter implements ObjectParameterInterface
{
    use ParameterTrait;

    public function __construct(string $className)
    {
        $this->type = typeObject($className);
        $this->attributes = new Set();
    }
}
