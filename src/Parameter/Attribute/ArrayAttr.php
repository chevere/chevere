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

namespace Chevere\Parameter\Attribute;

use ArrayAccess;
use Attribute;
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\ParameterAttributeInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\arrayp;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS_CONSTANT)]
class ArrayAttr implements ParameterAttributeInterface
{
    private ArrayParameterInterface $parameter;

    public function __construct(
        ParameterAttributeInterface ...$parameterAttribute,
    ) {
        $this->parameter = arrayp();
        foreach ($parameterAttribute as $name => $attribute) {
            $this->parameter = $this->parameter
                ->withRequired(
                    ...[
                        $name => $attribute->parameter(),
                    ]
                );
        }
    }

    // @phpstan-ignore-next-line
    public function __invoke(array|ArrayAccess $array): array|ArrayAccess
    {
        return ($this->parameter)($array);
    }

    public function parameter(): ParameterInterface
    {
        return $this->parameter;
    }
}
