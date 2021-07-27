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

namespace Chevere\Components\Parameter\Traits;

use Chevere\Components\Common\Traits\AttributesTrait;
use Chevere\Components\Common\Traits\DescriptionTrait;
use Chevere\Interfaces\Type\TypeInterface;

trait ParameterTrait
{
    use DescriptionTrait;
    use AttributesTrait;

    private TypeInterface $type;

    public function type(): TypeInterface
    {
        return $this->type;
    }

    public function description(): string
    {
        return $this->description;
    }
}
