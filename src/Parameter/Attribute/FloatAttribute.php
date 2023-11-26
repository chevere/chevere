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

use Attribute;
use Chevere\Parameter\Interfaces\FloatParameterInterface;
use Chevere\Parameter\Interfaces\ParameterAttributeInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\float;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS_CONSTANT)]
class FloatAttribute implements ParameterAttributeInterface
{
    private FloatParameterInterface $parameter;

    /**
     * @param float[] $accept
     */
    public function __construct(
        string $description = '',
        ?float $minimum = null,
        ?float $maximum = null,
        array $accept = [],
    ) {
        $this->parameter = float(
            description: $description,
            minimum: $minimum,
            maximum: $maximum,
            accept: $accept,
        );
    }

    public function __invoke(float $float): float
    {
        return ($this->parameter)($float);
    }

    public function parameter(): ParameterInterface
    {
        return $this->parameter;
    }
}
