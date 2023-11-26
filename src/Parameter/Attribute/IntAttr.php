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
use Chevere\Parameter\Interfaces\IntParameterInterface;
use Chevere\Parameter\Interfaces\ParameterAttributeInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use function Chevere\Parameter\int;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS_CONSTANT)]
class IntAttr implements ParameterAttributeInterface
{
    private IntParameterInterface $parameter;

    /**
     * @param int[] $accept
     */
    public function __construct(
        string $description = '',
        ?int $minimum = null,
        ?int $maximum = null,
        array $accept = [],
    ) {
        $this->parameter = int(
            description: $description,
            minimum: $minimum,
            maximum: $maximum,
            accept: $accept,
        );
    }

    public function __invoke(int $int): int
    {
        return ($this->parameter)($int);
    }

    public function parameter(): ParameterInterface
    {
        return $this->parameter;
    }
}
